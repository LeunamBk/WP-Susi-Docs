<?php

namespace Susi\Queries;
use DOMDocument;
/**
 * @package     Joomla.Administrator
 * @subpackage  com_protocols
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('WPINC') or die('Restricted access');

define('SUSITABLE', 'wp_susidocs');

/**
 * ProtocolsModel
 *
 * @since  0.0.1
 */
class ProtocolsModel {


    public static function getYearsList()
    {

        // get a db connection.
        global $wpdb;

        // query for data
        $query =
            " SELECT DISTINCT DATE_FORMAT(date,'%Y') as year    ".
            " FROM ".SUSITABLE                                   .
            " ORDER BY DATE_FORMAT(date,'%Y') DESC;             ";

        // load and fetch data
        $years = $wpdb->get_results((string) $query, OBJECT);

        // push all data value entry
        array_unshift($years, array("year" => "Alle"));

        return $years;
    }

    /**
     * Method to get a list with metainformation
     * to each protocol in the requested context.
     *
     * @return  array.
     */
    public static function getProtocolsByCategory($mode = '%')
    {

        // Get a db connection.
        global $wpdb;

        // Query for data
        $query =
            " SELECT id, DATE_FORMAT(date,'%d-%m-%Y') as date, context  ".
            " FROM ".SUSITABLE                                           .
            " WHERE context LIKE '$mode'                                ".
            " ORDER BY DATE_FORMAT(date,'%Y-%m-%d') desc;               ";

        // Load and fetch data
        $protocols = $wpdb->get_results((string) $query, OBJECT);

        return $protocols;

    }

    public static function getProtocolsBySelect($mode, $year, $month)
    {
        // Get a db connection.
        global $wpdb;

        // Query for data
        $query =
            " SELECT id, DATE_FORMAT(date,'%d-%m-%Y') as date, context  ".
            " FROM ".SUSITABLE                                           .
            " WHERE context LIKE '$mode'                                ".
            " AND DATE_FORMAT(date,'%Y') LIKE '$year'                   ".
            " AND MONTH(date) LIKE '$month'                             ".
            " ORDER BY DATE_FORMAT(date,'%Y-%m-%d') desc;               ";

        // Load and fetch data
        $protocols = $wpdb->get_results((string) $query, OBJECT);

        return $protocols;

    }


    public static function getProtocolsBySearch($mode, $search, $year, $month)
    {

        // Get a db connection.
        global $wpdb;

        // escape search
        $searchEsc = "%".esc_sql( $wpdb->esc_like( $search ) )."%";

        // Query for data
        $query = $wpdb->prepare(
            " SELECT id, DATE_FORMAT(date,'%%d-%%m-%%Y') as date, context, body    ".
            " FROM ".SUSITABLE                                                      .
            " WHERE context LIKE %s                                                ".
            " AND (body LIKE %s)                                                   ".
            " AND (YEAR(date) LIKE %s)                                             ".
            " AND (MONTH(date) LIKE %s)                                            ".
            " ORDER BY DATE_FORMAT(date,'%%Y-%%m-%%d') desc;                       ",
            array($mode, $searchEsc, $year, $month));

        // Load and fetch data
        $protocols = $wpdb->get_results($query, OBJECT);

        if(is_null($protocols)) return;

        // get paragraph of all matches which include search string
        $protocolsSearchSnippets = self::getSearchTextSnippets($protocols, $search);

        // exclude protocols body text from results
        $protocolsMeta = self::removeBodyTextFromResultset($protocols);

        return compact('protocolsMeta', 'protocolsSearchSnippets');

    }

    public static function getProtocolText($id = 1, $search = false)
    {

        // get susi table instance in
        // NOTE: in comparison to the above methods,
        // this approach is used for returning only one row
        global $wpdb;

        // Query for data
        $query =
            " SELECT id, DATE_FORMAT(date,'%d-%m-%Y') as date, context, body    ".
            " FROM ".SUSITABLE                                                   .
            " WHERE id LIKE '$id';                                              ";

        // Load and fetch data
        $protocols = $wpdb->get_results((string) $query, OBJECT);

        return $protocols[0]->body;


    }

    // for every paragraph
    private static function getSearchTextSnippets($protocols, $search){

        // IMPORTANT: ensure proper string encoding!
        $search = utf8_encode($search);

        $protocolsSearchSnippets = array();

        foreach($protocols as $protocol){

            $DOM = self::loadHtml($protocol->body);
            $snippets = array();

            //get and iterate over all p html paragraphs
            $paras = $DOM->getElementsByTagName('p');
            foreach ($paras as $para) {
                $textContent = $para->textContent;

                // search for search string in paragraph -> case insesitive, for sensitive see strpos()
                if (stripos($textContent, $search) !== FALSE) {
                    // surround search string with span
                    $searchP = self::spanSearchString($textContent, $search);

                    array_push($snippets, utf8_decode($searchP));
                }
            }

            if(!empty($snippets)){
                $protocolsSearchSnippets[$protocol->id] = [ 'id' => $protocol->id,
                                                            'context'=> $protocol->context,
                                                            'date' => $protocol->date,
                                                            'text' => $snippets];
            }
        }

        return $protocolsSearchSnippets;
    }

    private static function loadHtml($html, $suppressWarnings = TRUE){
        $DOM = new DOMDocument;

        // if xml html string is not well formed e.g. opening tags without closing tags,
        // which is not a problem in modern browsers, e.g. <br> tag instead of <br/> load html
        // throws warnings, this is surpressed by the underlying routine
        if($suppressWarnings){
            // modify state
            $libxml_previous_state = libxml_use_internal_errors(true);

            $DOM->loadHTML($html);

            // handle errors
            libxml_clear_errors();

            // restore
            libxml_use_internal_errors($libxml_previous_state);
        } else {
            $DOM->loadHTML($html);
        }
        return $DOM;
    }

    private static function spanSearchString($text, $search){
        $spanOpen = "<span class='searchString'>";
        $begin = stripos($text, $search);
        $end = $begin + strlen($search) + strlen($spanOpen);
        $text = substr_replace($text, $spanOpen, $begin, 0);
        $text = substr_replace($text, "</span>", $end, 0);
        return $text;
    }

    private static function removeBodyTextFromResultset($protocols){

        $protocolsMeta = array();
        foreach($protocols as $key => $protocol){
            $line = array('id' => $protocol->id, 'date' => $protocol->date, 'context' => $protocol->context);
            array_push($protocolsMeta, $line);
        }

        return $protocolsMeta;
    }

}