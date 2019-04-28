</div>

    <!-- Optional JavaScript -->
    <!-- jQuery first-->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="./jquerymaskedinput.js"></script>
    <script>
        $.mask.definitions['h'] = "[0-9]";
        jQuery(function($){
           $(".time").mask("hh:hh");
        });
    
		$("#ad_ex").click(function(e){
			e.preventDefault();
			$("#new_ex").append('<div class="form-group row"><label for="begin" class="col-sm-2">Початок</label><input type="text" class="form-control time col-sm-2" name="begin[]" value="" required><label for="end" class="col-sm-2">Кінець</label><input type="text" class="form-control time col-sm-2" name="end[]" value="" required><label for="noe" class="col-sm-2">Номер заняття</label><input type="text" class="form-control col-sm-2" name="noe[]" value="" required> </div>');
			jQuery(function($){
               $(".time").mask("hh:hh");
            });
		});
	</script>
  </body>
</html>