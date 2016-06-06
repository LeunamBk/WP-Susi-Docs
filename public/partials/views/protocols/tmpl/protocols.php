<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_protocols
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// load assets

$ASSETSPATH = plugins_url() .'/'. $this->plugin_name . '/public/';

//wp_enqueue_style( 'dasd', $ASSETSPATH . "css/cssreset.css", array());
wp_enqueue_style( 'docs-bootstrap', $ASSETSPATH . "css/bootstrap.min.css", array());
wp_enqueue_style( 'docs-taskman', $ASSETSPATH . "css/taskman.css", array());

wp_enqueue_script( 'docs-angular', $ASSETSPATH . "js/angular.min.js", array());
wp_enqueue_script( 'docs-app', $ASSETSPATH . "js/app.js", array());
wp_enqueue_script( 'docs-services', $ASSETSPATH . "js/services.js", array());
wp_enqueue_script( 'docs-controllers', $ASSETSPATH . "js/controllers.js", array());

?>

<script type="text/javascript">
    var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>"
</script>

<div ng-app="myApp">
    <div ng-controller="tasksController">

        <div class="row">
            <div class="container">
                <!--blockquote><h1><a href="#">Susis Protokolle</a></h1></blockquote-->
                <div class="col-sm-12">

                    <div class="widget-box" id="recent-box" ng-controller="tasksController">
                        <div class="widget-header header-color-blue">
                            <div class="row">

                                <div class="col-sm-3">
                                    <h4 class="bigger lighter">
                                        <i class="glyphicon glyphicon-align-justify"></i>&nbsp;
                                        Susis Protokolle
                                    </h4>
                                </div>

                                <div class="col-sm-2">
                                    <label for="mySelect" class="header-elements-margin">Context:</label>
                                    <select name="mySelect" id="mySelect" class=" header-elements-margin"
                                            ng-options="option.name for option in data.availableOptions track by option.id"
                                            ng-model="data.selectedOption"
                                            ng-change="filterDocs()"></select>
                                </div>

                                <div class="col-sm-3">
                                    <label for="mySelect" class="header-elements-margin">Timeframe:</label>
                                    <!--input type="month" class="header-elements-margin"
                                           ng-model="filterTime"
                                           ng-change="filterByTime()"-->

                                    <div>
                                        <select class="select-month header-elements-margin" name="select-month"
                                                ng-options="option.name for option in data.monthList track by option.id"
                                                ng-model="data.selectedMonth"
                                                ng-change="filterDocs()">
                                        </select>

                                        <select class="select-year header-elements-margin" name="select-year"
                                                ng-options="option.year for option in yearsList track by option.year"
                                                ng-model="data.selectedYear"
                                                ng-change="filterDocs()">
                                        </select>

                                    </div>
                                </div>

                                <!--div class="col-sm-3">
                                    <button ng-click="addNewClicked=!addNewClicked;" class="btn btn-sm btn-danger header-elements-margin"><i class="glyphicon  glyphicon-plus"></i>&nbsp;Add New Task</button>
                                </div-->
                                <div class="row">
                                    <div class="col-sm-3">

                                        <div class="input-group">
                                            <label class=" header-elements-margin" >Suche:</label>
                                            <br>
                                            <input type="text" ng-model="filterTasks" class="search header-elements-margin" ng-keyup="$event.keyCode == 13 && filterDocs()" placeholder="Filter Documents">
                                            <a class="clear" ng-click="clearSearch()">
                                                <span class="glyphicon glyphicon-remove"></span>
                                            </a>
                                            <button type="submit" class="btn btn-default" name="submit" id="searchsubmit" value="Go" ng-click="filterDocs()">
						                    <span class="glyphicon glyphicon-search">
                                            </span>
                                            </button>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="widget-body ">
                            <!--form ng-init="addNewClicked=false; " ng-if="addNewClicked" id="newTaskForm" class="add-task">
                                <div class="form-actions">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="comment" ng-model="taskInput" placeholder="Add New Task" ng-focus="addNewClicked">
                                        <div class="input-group-btn">
                                            <button class="btn btn-default" type="submit" ng-click="addTask(taskInput)"><i class="glyphicon glyphicon-plus"></i>&nbsp;Add New Task</button>
                                        </div>
                                    </div>
                                </div>
                            </form-->

                            <div class="col-sm-3 doc-select">
                                <div class="task">
                                    <label ng-repeat="task in tasks | filter : filterTask" class="checkbox" data-ng-class="{'active' : $first}" ng-init="$first && toggleStatus(task.id,task.STATUS, task.TASK)" ng-class="{selected: task.id === idSelected}" ng-click="toggleStatus(task.id,task.STATUS, task.TASK)">
                                        <span class="list-category">{{ dataModel.contextMap[task.context]}}</span><span class="list-date"><b>{{task.date}}</b></span>
                                        <!--a ng-click="deleteTask(task.ID)" class="pull-right"><i class="glyphicon glyphicon-trash"></i></a-->
                                    </label>

                                    <label ng-repeat="task in searchTasks | filter : filterTask" class="checkbox"  ng-class="{selected: task.id === idSelected}" ng-click="toggleStatus(task.id,task.STATUS, task.TASK)">
                                        <span class="list-category">{{ dataModel.contextMap[task.context]}}</span><span class="list-date"><b>{{task.date}}</b></span>
                                        <!--a ng-click="deleteTask(task.ID)" class="pull-right"><i class="glyphicon glyphicon-trash"></i></a-->
                                    </label>

                                </div>
                            </div>
                            <div class="col-sm-9">
                                <div class="protocol-textbox" ng-hide="hideFullProtocol">
                                    <div class="formater" style="visibility:hidden;"></div>
                                    <div ng-bind-html="fullProtocol"></div>
                                </div>

                                <div class="protocol-textbox" ng-repeat="snippet in protocolsText"  ng-hide="!hideFullProtocol">
                                    <div class="formater search" ng-click="toggleStatus(snippet.id,snippet.STATUS, snippet.TASK)">{{ dataModel.contextMap[snippet.context]}} {{snippet.date}}</div>
                                    <div ng-bind-html="getSnippet(snippet.text)"></div>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>