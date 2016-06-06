<?php
namespace Susi\Requests;
use Susi\Queries\ProtocolsModel;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}


class ProtocolsControllerRequest
{

    public static function yearsDropDown()
    {
        // call model and get data
        $years = ProtocolsModel::getYearsList();
        return json_encode($years);

    }

    public static function protocolsBySearch()
    {

        // get mode argument from url
        if ( isset( $_GET['mode'] )) {
            $mode = intval($_GET['mode']);
        }

        if ( isset( $_GET['search'] )) {
            $search = $_GET['search'];
        }

        if ( isset( $_GET['year'] )) {
            $year = intval($_GET['year']);
        }

        if ( isset( $_GET['month'] )) {
            $month = intval($_GET['month']);
        }


        // if 0 query for all
        if($mode == 0) $mode = '%';
        if($year == 0) $year = '%';
        if($month == 0) $month = '%';

        // call model and get data
        $pList = ProtocolsModel::getProtocolsBySearch($mode, $search, $year, $month);

        // return data
        return json_encode($pList);

    }

    public static function protocolsBySelect()
    {

        $mode = $year = $month = '%';

        // get mode argument from url
       if ( isset( $_GET['mode'] )) {
            $mode = intval($_GET['mode']);
        }

        if ( isset( $_GET['year'] )) {
            $year = intval($_GET['year']);
        }

        if ( isset( $_GET['month'] )) {
            $month = intval($_GET['month']);
        }

        // if 0 query for all
        if($mode == 0) $mode = '%';
        if($year == 0) $year = '%';
        if($month == 0) $month = '%';


       // call model and get data
       $pList = ProtocolsModel::getProtocolsBySelect($mode, $year, $month);

        // return data
        return json_encode($pList);

    }

    public static function protocolText(){

        // get mode argument from url
        if ( isset( $_GET['id'] )) {
            $id = intval($_GET['id']);
        }

        // call model and get data
        $text = ProtocolsModel::getProtocolText($id);

        return var_export($text);

    }

}