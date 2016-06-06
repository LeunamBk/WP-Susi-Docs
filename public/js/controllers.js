app.controller('tasksController', ['$scope', '$http', '$sce', 'Request' , function($scope, $http, $sce, Request){

    filterDataBySelect(0,0,0); // Load all available tasks
    getYears(); // Load all available years with data for dropdown select

    function getTask(){
        Request.fetchData({action:'protocolsByCategory'}).then(function(data){
            $scope.tasks = data;
        });
    };

    function getYears(){
        Request.fetchData({action:'yearsDropDown'}).then(function(data){
            $scope.yearsList = data;
            $scope.option = $scope.yearsList[0].year;
        });
    };

    $scope.filterDocs = function(){
        var context = $scope.data.selectedOption['id'];
        var month = $scope.data.selectedMonth['id'];
        var year = $scope.data.selectedYear['year'];
        var search = $scope.filterTasks;
        if(year == "Alle") year = 0;

        if(typeof search === "undefined" || search === ""){
            filterDataBySelect(context, year, month);
        } else {
            filterDataBySearch(context, year, month, search);
        }

    };

    function filterDataBySelect(context, year, month){
        Request.fetchData({action:'protocolsBySelect', mode:context, year:year, month:month}).then(function(data){
            $scope.tasks = data;
        });
    }

    function filterDataBySearch(context, year, month, search){
        Request.fetchData({action:'protocolsBySearch', mode:context, year:year, month:month, search:search}).then(function(data){
            $scope.tasks = [];
            $scope.searchTasks = data.protocolsMeta;
            $scope.protocolsText = data.protocolsSearchSnippets;
            $scope.hideFullProtocol = true;
        });
    }

    $scope.toggleStatus = function(id){

        //highlight clicked feature
        $scope.idSelected = id;

        Request.fetchData({action:'protocolText', id:id}).then(function(data){
            var search = $scope.filterTasks;
            $scope.html = data.replace (/(^')|('$)/g, '');

            //highlight search string in doc if availabel
            if(typeof search !== "undefined" && search !== ""){
                var regEx = new RegExp(search, "ig");
                var replaceMask ="<span class='searchString'>"+search+"</span>";
                $scope.html = $scope.html.replace(regEx, replaceMask);
            }

            $scope.fullProtocol = $sce.trustAsHtml($scope.html);
            $scope.hideFullProtocol = false;
        });

    };

    $scope.clearSearch = function(){
        var context = $scope.data.selectedOption['id'];
        var month = $scope.data.selectedMonth['id'];
        var year = $scope.data.selectedYear['year'];
        if(year == "Alle") year = 0;

        $scope.filterTasks = null;

        filterDataBySelect(context, year, month);
    };

    $scope.getSnippet = function(snippet){
        if(snippet.length > 1){
            var concatSnippet = snippet[0];
            for(var i = 1; i < snippet.length; i++){
                concatSnippet += "<br><br>[...]<br><br>" + snippet[i];
            }
            return  $sce.trustAsHtml(concatSnippet);
        } else {
            return $sce.trustAsHtml(snippet[0])
        }
    };

    $scope.data = {
        availableOptions: [
            {id: '0', name: 'Alle'},
            {id: '1', name: 'MV'},
            {id: '2', name: 'Bauko'},
            {id: '3', name: 'Proko'},
            {id: '4', name: 'Sonstige'}
        ],
        selectedOption: {id: '0', name: 'Alle'}, // set default

        monthList: [
            {id: '0', name: 'Alle'},
            {id: '1', name: 'Januar'},
            {id: '2', name: 'Februar'},
            {id: '3', name: 'März'},
            {id: '4', name: 'April'},
            {id: '5', name: 'Mai'},
            {id: '6', name: 'Juni'},
            {id: '7', name: 'Juli'},
            {id: '8', name: 'August'},
            {id: '9', name: 'September'},
            {id: '10', name: 'Oktober'},
            {id: '11', name: 'November'},
            {id: '12', name: 'Dezember'}
        ],
        selectedMonth: {id: '0', name: 'Alle'}, // set default
        selectedYear: {year: 'Alle'} // set default

    };

    $scope.dataModel = {
        contextMap: {
            0: 'Alle',
            1: 'MV',
            2: 'Bauko',
            3: 'Proko'}

    };


}]);
