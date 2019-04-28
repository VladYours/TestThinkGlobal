<?php

	

class Main {
	
	//access to DataBase
	const host="vikamade.mysql.tools";
	const login="vikamade_tg";
	const password="vAo*%Yt841";
	const db="vikamade_tg";
	
	//clear input
	function ci($a){
		return intval($a);	
	}
	
	//connect to db or print error message
	function connect(){
		$mysqli = new mysqli(self::host,self::login,self::password,self::db);
		if ($mysqli->connect_errno) {
    
			echo "Ошибка: Не удалась создать соединение с базой MySQL: \n";
			echo "Номер ошибки: " . $mysqli->connect_errno . "\n";
			echo "Ошибка: " . $mysqli->connect_error . "\n";
			exit;
		}
		return $mysqli;
	}
	
	//view list of timetables
	function view_all_timetable(){
		
		$con=$this->connect();
		
		$sql="SELECT * FROM timetable";
		
		
		$res=$con->query($sql);
		
		if ($res->num_rows>0){
			echo "<div class='row'>\n";
			while ($timetable = $res->fetch_assoc()) {
				echo "<div class='col-md-8 col-sm-12'>".$timetable["Name"]."</div>";
				echo "<div class='col-md-2 col-sm-6'><a href='./index.php?view=".$timetable["iTT"]."'>Переглянути</a></div>";
				echo "<div class='col-md-2 col-sm-6'><a href='./index.php?del=".$timetable["iTT"]."'>Видалити</a></div>";
			}
			echo "</div>\n";
		} else {
			echo "<div class='alert alert-danger text-center'>Розклади відсутні</div>";
		}
		
	}
	
	//view some timetable 
	function view_timetable($iTT){
		
		$con=$this->connect();
		
		$sql="SELECT * FROM exercises WHERE timetable=".$iTT." ORDER BY noe ASC";
		
		
		$res=$con->query($sql);
		
		if ($res->num_rows>0){
			echo "<ul class='row'>\n";
			while ($exercise = $res->fetch_assoc()) {
				
				//print exercise data
				echo "<li class='col-sm-12'>".$exercise["begin"]." - ".$exercise["end"]." - Заняття ".$exercise["noe"]."</li>";
			}
			echo "</ul>\n";
		} else {
			echo "<div class='alert alert-danger text-center'>У розкладі відсутні заняття<br/><a href='./index.php'>Повернутися на головну сторінку</a></div>";
		}
		
	}
	
	//delete timetable from db
	function delete_timetable($iTT){
		
		$con=$this->connect();
		
		//delete all exercises in timetable
		$sql="DELETE FROM exercises WHERE timetable=".$iTT;		
		$res1=$con->query($sql);
		//delete timetable
		$sql="DELETE FROM timetable WHERE iTT=".$iTT;		
		$res2=$con->query($sql);
		
		if (! ($res1&$res2)){
			echo "<div class='alert alert-danger text-center'>Нажаль, розклад не вдалося видалити. Зверніться до адміністратора за допомогою<br/><a href='./index.php'>Повернутися на головну сторінку</a></div>";
		} else {
			echo "<div class='alert alert-success text-center'>Розклад успішно видалено.<br/><a href='./index.php'>Повернутися на головну сторінку</a></div>";
		}
		
	}
	
	function add_timetable_form(){
		
		
		echo "<form method='POST' class='mt-2 text-center '>\n";
		echo '<div class="form-group row">
					<label for="title" class="col-sm-4" >Назва розкладу</label>
					<input type="text"  class="form-control  col-sm-8" name="title" value="" required>					
				  </div>'."\n";
		echo "<div id='new_ex'>\n";
		echo '<div class="form-group row">
					<label for="begin" class="col-sm-2" >Початок</label>
					<input type="text"  class="form-control time col-sm-2" name="begin[]" value="" required>
					<label for="end" class="col-sm-2" >Кінець</label>
					<input type="text"  class="form-control time col-sm-2" name="end[]" value="" required>
					<label for="noe" class="col-sm-2" >Номер заняття</label>
					<input type="text"  class="form-control col-sm-2" name="noe[]" value="" required>
				  </div>'."\n";	
		echo "</div>\n";
		echo '<button id="ad_ex" class="btn btn-primary  mr-2">Додати заняття</button>';
		echo '<button type="submit" class="btn btn-success ">Зберегти розклад</button>';
		echo "</form>\n";		
		
		
	}
	
	function add_timetable($arr){
		
		$check=true;
		
		$con=$this->connect();
        
        $error_message="";
        
        //check title of timetable
        if($arr["title"]==""){
            $check=false; 
            $error_message.=" <b>Немає назви розкладу!</b> ";  
        }
        
        //check cross of exercise in timetable
        $stop=count($arr["noe"]);
        for($i=1;$i<$stop;$i++){
            //current time of exercise
            $begin_cur=strtotime($arr["begin"][$i]);
            $end_cur=strtotime($arr["end"][$i]);
            for($j=($i+1);$j<$stop;$j++){
                //check time of exercise
                $begin=strtotime($arr["begin"][$j]);
                $end=strtotime($arr["end"][$j]);
                //if exist cross of timetable - error message
                if ((($begin_cur>$begin)and($begin_cur<$end))or(($end_cur>$begin)and($end_cur<$end))){
                    $check=false; 
                    $error_message.=" <b>Час окремих занять перехрещується!</b> ";               
                }
            
            }
        }
        
        //check order of exercise in timetable
        $prev=intval($arr["noe"][0]);       
        for($i=1;$i<$stop;$i++){
            //if previuous number more than current - error message
            if ($prev>intval($arr["noe"][$i])){
                $check=false;
                $error_message.=" <b>Номера занять не проставлені в порядку збільшення!</b> ";
            }
        }
        
        //check all exercise that begin earlier then end
        for($i=0;$i<$stop;$i++){
            $begin=strtotime($arr["begin"][$i]);
            $end=strtotime($arr["end"][$i]);
            //if begin more than end - error message
            if ($begin>$end){
                $check=false;
                $error_message.=" <b>У одному з занять час початку більше за час закінчення заняття!</b> ";
            }
        }
        
        //insert new timetable in database
        if ($check){
		
    		$sql="INSERT INTO timetable (Name) VALUES ('".$arr["title"]."')";
    		$con->query($sql);
    		
    		$timetable=$con->insert_id;
    		
    		$str="";
    		foreach ($arr["begin"] as $key=>$val){
    			$str.="('".$val."','".$arr["end"][$key]."',".$timetable.",".$arr["noe"][$key]."),";
    		}
    			
    		$sql="INSERT INTO exercises (begin, end, timetable, noe) VALUES ".rtrim($str, ",");
    		$con->query($sql);
        }		
		
        //iform user about result of operation
		if ($check){
			echo "<div class='alert alert-success text-center'>Поздоровляємо! Розклад створено!<br/><a href='./index.php'>Повернутися на головну сторінку</a></div>";
		} else {
			echo "<div class='alert alert-danger text-center'>Нажаль, розклад не вдалося зберегти.<br/>Через: ".$error_message."<br/>Зверніться до адміністратора за допомогою<br/><a href='./index.php'>Повернутися на головну сторінку</a></div>";
		}
		
	}
	
}

?>