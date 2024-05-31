<?php

function getstepList($actual_cat_Id){
    $mysqli= ConnectionFactory::GetConnection(); 
    $stmt = $mysqli->prepare("SELECT  CS1.Id, 
                                      CS1.Name, 
                                      CS1.CategoryStepsTypeId,
                                      in_step_1_id,
                                      rank_in_step_1 ,
                                      Idin_step_2_id,
                                      rank_in_step_2 
                               FROM CategoryStep CS1 
                               LEFT OUTER JOIN StepLinking ON CS1.Id=StepLinking.out_step_id
                               WHERE CS1.ActualCategoryId=? ORDER BY CS1.Id DESC");
     $stmt->bind_param("i", $actual_cat_Id );
     $stmt->bind_result( $step_id, $step_name, $step_type, $parent_id_1,$parent_rank_1, $parent_id_2,$parent_rank_2);
     $stmt->execute();
     $res = array();
     while ($stmt->fetch()){
         $res[$step_id] = array("name"=>$step_name,"type"=>$step_type, "parent_1"=>$parent_id_1, "parent_rank_1"=>$parent_rank_1, "parent_2"=>$parent_id_2, "parent_rank_2"=>$parent_rank_2 );
     }
     $stmt->close();
     return $res;
}

function plot_pool_3($step_id, $catName, $stepName){
    echo '<svg height="500" width="600">
        <line x1="0" y1="200" x2="500" y2="200" style="stroke:black;stroke-width:2" />
        <line x1="0" y1="300" x2="500" y2="300" style="stroke:black;stroke-width:1" />
        <line x1="0" y1="400" x2="500" y2="400" style="stroke:black;stroke-width:1" />
        <line x1="0" y1="500" x2="500" y2="500" style="stroke:black;stroke-width:2" />
        <line x1="200" y1="0" x2="200" y2="500" style="stroke:black;stroke-width:2" />
        <line x1="300" y1="0" x2="300" y2="500" style="stroke:black;stroke-width:1" />
        <line x1="400" y1="0" x2="400" y2="500" style="stroke:black;stroke-width:1" />
        <line x1="500" y1="0" x2="500" y2="500" style="stroke:black;stroke-width:2" />
        <line x1="300" y1="200" x2="500" y2="400" style="stroke:black;stroke-width:1" />
        <line x1="400" y1="200" x2="500" y2="300" style="stroke:black;stroke-width:1" />
        <line x1="200" y1="300" x2="400" y2="500" style="stroke:black;stroke-width:1" />
        <line x1="200" y1="400" x2="300" y2="500" style="stroke:black;stroke-width:1" />
        <rect width="100" height="100" x="200" y="200"  fill="gray" />
        <rect width="100" height="100" x="300" y="300"  fill="gray" />
        <rect width="100" height="100" x="400" y="400"  fill="gray" />';
        
        echo '<text x="5" y="180" fill="black" font-size="25" transform="translate(20 0)rotate(-45 0,200)">'.$catName.'</text>';
        echo '<text x="35" y="210" fill="black" font-size="25" transform="translate(20 0)rotate(-45 0,200)">'.$stepName.'</text>';
        echo '<text x="5" y="240" fill="black" font-size="20" >Name</text>
              <text x="5" y="270" fill="black" font-size="20" >Prénom</text>
               <text x="5" y="240" fill="black" font-size="20" transform=" translate(200 0)rotate(-90 0,200)">Name 1</text>
               <text x="5" y="270" fill="black" font-size="20" transform=" translate(200 0)rotate(-90 0,200)">Prénom 1</text>';
               
       echo '<text x="100" y="340" fill="black" font-size="20"  text-anchor="middle" >Name</text>
              <text x="100" y="370" fill="black" font-size="20"  text-anchor="middle">Prénom</text>
               <text x="100" y="340" fill="black" font-size="20"  text-anchor="middle" transform=" translate(200 0)rotate(-90 0,200)">Name 1</text>
               <text x="100" y="370" fill="black" font-size="20" text-anchor="middle"  transform=" translate(200 0)rotate(-90 0,200)">Prénom 1</text>';
               
                echo '<text x="5" y="440" fill="black" font-size="20" >Name</text>
              <text x="5" y="470" fill="black" font-size="20" >Prénom</text>
               <text x="5" y="440" fill="black" font-size="20" transform=" translate(200 0)rotate(-90 0,200)">Name 1</text>
               <text x="5" y="470" fill="black" font-size="20" transform=" translate(200 0)rotate(-90 0,200)">Prénom 1</text>';
   
         
       // resultats 1 vs 2  
       echo '<text x="310" y="280" fill="black" font-size="25">0</text><text x="340" y="230" fill="black" font-size="25">1(10)</text>';   
       echo '<text x="210" y="380" fill="black" font-size="25">1(10)</text><text x="240" y="330" fill="black" font-size="25">0</text>';    
        
      echo '<text x="510" y="250" fill="black" font-size="25">0(0)</text>';  
      echo '<text x="510" y="350" fill="black" font-size="25">1(10)</text>';  
      echo '<text x="510" y="450" fill="black" font-size="25">0(0)</text>';  
      
         
        echo'
      </svg>
    ';
}
plot_pool_3(7,'Cat Name', 'Step Name');
?>
