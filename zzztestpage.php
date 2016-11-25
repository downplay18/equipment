<body>

    <?php include 'main_head.php'; ?>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.0.3/js/bootstrap.min.js"></script>
    
    <link href="http://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    
    <link href="http://cdn.rawgit.com/davidstutz/bootstrap-multiselect/master/dist/css/bootstrap-multiselect.css" rel="stylesheet" type="text/css" />
    <script src="http://cdn.rawgit.com/davidstutz/bootstrap-multiselect/master/dist/js/bootstrap-multiselect.js" type="text/javascript"></script>
    
    
    <script type="text/javascript">
        $(function () {
            $('#lstFruits').multiselect({
                includeSelectAllOption: true
            });

        });
    </script>
    
    
    <select id="lstFruits" multiple="multiple">
        <option value="cheese" selected>Cheese</option>
        <option value="tomatoes">Tomatoes</option>
        <option value="mozarella">Mozzarella</option>
        <option value="mushrooms" selected>Mushrooms</option>
        <option value="pepperoni" selected>Pepperoni</option>
        <option value="onions">Onions</option>
    </select>  


    
    
    <script src="js/jquery-1.12.3.js" type="text/javascript"></script>
    <script src="js/bootstrap.min.js"></script>

    <script src="js/jquery-ui.min.js" type="text/javascript"></script>

 <script src="//code.jquery.com/jquery-1.12.3.js" type="text/javascript"></script> 
    <script src="datatables/jquery.dataTables.min.js" type="text/javascript"></script>  
    <script src="datatables/dataTables.buttons.min.js" type="text/javascript"></script>  
     <script src="//cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js" type="text/javascript"></script> 
    <script src="datatables/jszip.min.js" type="text/javascript"></script> 
     <script src="datatables/pdfmake.min.js" type="text/javascript"></script>  
    <script src="datatables/vfs_fonts.js" type="text/javascript"></script>  
    <script src="datatables/buttons.html5.min.js" type="text/javascript"></script> 
    <script src="datatables/buttons.print.min.js" type="text/javascript"></script> 

    <script src="datatables/buttons.bootstrap.min.js" type="text/javascript"></script> 
    <script src="datatables/buttons.colVis.js" type="text/javascript"></script>

     Chosen 
    <script src="js/chosen.jquery.js" type="text/javascript"></script>



    <script src="bootstrap-daterangepicker/moment.min.js" type="text/javascript"></script>
    <script src="bootstrap-daterangepicker/daterangepicker.js" type="text/javascript"></script>


</body>