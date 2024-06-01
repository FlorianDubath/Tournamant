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



<svg height="502" width="600">

<rect width="200" height="100" x="0" y="200"   style="fill:rgb(230,230,230);"/>
<rect width="200" height="100" x="0" y="300"   style="fill:rgb(230,230,230);"/>
<rect width="200" height="100" x="0" y="400"   style="fill:rgb(230,230,230);"/>


<rect width="200" height="100" x="0" y="200" transform=" translate(200 0)rotate(-90 0,200)"  style="fill:rgb(230,230,230);"/>
<rect width="200" height="100" x="0" y="300" transform=" translate(200 0)rotate(-90 0,200)"  style="fill:rgb(230,230,230);"/>
<rect width="200" height="100" x="0" y="400" transform=" translate(200 0)rotate(-90 0,200)"  style="fill:rgb(230,230,230);"/>


        <rect width="100" height="100" x="200" y="200"  fill="gray" />
        <rect width="100" height="100" x="300" y="300"  fill="gray" />
        <rect width="100" height="100" x="400" y="400"  fill="gray" />
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
        
        
        <text x="100" y="100" fill="black" font-size="20" transform="rotate(-45 100,100)" text-anchor="middle">Nom de la Catégorie -81</text>
        
        
        <text x="120" y="120" fill="black" font-size="25" transform="rotate(-45 120,120)" text-anchor="middle">Groupe A</text>
        
        <text x="100" y="240" fill="black" font-size="20" text-anchor="middle">Name</text>
        <text x="100" y="270" fill="black" font-size="20" text-anchor="middle">Prénom</text>
        <text x="100" y="240" fill="black" font-size="20" text-anchor="middle" transform=" translate(200 0)rotate(-90 0,200)">Name 1</text>
        <text x="100" y="270" fill="black" font-size="20" text-anchor="middle" transform=" translate(200 0)rotate(-90 0,200)">Prénom 1</text>
       
        <text x="100" y="340" fill="black" font-size="20"  text-anchor="middle" >Name</text>
        <text x="100" y="370" fill="black" font-size="20"  text-anchor="middle">Prénom</text>
        <text x="100" y="340" fill="black" font-size="20"  text-anchor="middle" transform=" translate(200 0)rotate(-90 0,200)">Name 1</text>
        <text x="100" y="370" fill="black" font-size="20" text-anchor="middle"  transform=" translate(200 0)rotate(-90 0,200)">Prénom 1</text>
               
        <text x="100" y="440" fill="black" font-size="20" text-anchor="middle">Name</text>
        <text x="100" y="470" fill="black" font-size="20" text-anchor="middle">Prénom</text>
        <text x="100" y="440" fill="black" font-size="20" text-anchor="middle" transform=" translate(200 0)rotate(-90 0,200)">Name 1</text>
        <text x="100" y="470" fill="black" font-size="20" text-anchor="middle" transform=" translate(200 0)rotate(-90 0,200)">Prénom 1</text>
               
               
        <text x="338" y="280" fill="black" font-size="25" text-anchor="middle">0</text>
        <text x="362" y="230" fill="black" font-size="25" text-anchor="middle">2(10)</text>
        <text x="238" y="380" fill="black" font-size="25" text-anchor="middle">2(10)</text>
        <text x="262" y="330" fill="black" font-size="25" text-anchor="middle">0</text>
        
        <text x="438" y="280" fill="black" font-size="25" text-anchor="middle">0</text>
        <text x="462" y="230" fill="black" font-size="25" text-anchor="middle">2(7)</text>
        <text x="238" y="480" fill="black" font-size="25" text-anchor="middle">2(7)</text>
        <text x="262" y="430" fill="black" font-size="25" text-anchor="middle">0</text>
      
      <text x="510" y="250" fill="black" font-size="25">0(0)</text>
      
      <text x="510" y="350" fill="black" font-size="25">2(10)</text>
      
      <text x="510" y="450" fill="black" font-size="25">0(0)</text>'
        
      </svg>
      <br/>
      <br/>
<svg height="200" width="650">

        <rect width="200" height="80" x="200" y="10"   style="fill:rgb(230,230,230);stroke-width:2;stroke:black"/>
        
        <rect width="200" height="80" x="200" y="110"   style="fill:rgb(230,230,230);stroke-width:2;stroke:black"/>
        
        <line x1="400" y1="50" x2="422" y2="50" style="stroke:black;stroke-width:4" />
        <line x1="400" y1="150" x2="422" y2="150" style="stroke:black;stroke-width:4" />
        <line x1="420" y1="50" x2="420" y2="150" style="stroke:black;stroke-width:4" />
        <line x1="420" y1="100" x2="440" y2="100" style="stroke:black;stroke-width:4" />
        <rect width="200" height="80" x="440" y="60"   style="fill:rgb(230,230,230);stroke-width:2;stroke:black"/>
        
	<text x="100" y="40" fill="black" font-size="20" text-anchor="middle">Groupe A</text>
	<text x="100" y="60" fill="black" font-size="20" text-anchor="middle">1er</text>
        <text x="100" y="140" fill="black" font-size="20" text-anchor="middle">Groupe B</text>
        <text x="100" y="160" fill="black" font-size="20" text-anchor="middle">1er</text>
        
        <text x="300" y="40" fill="black" font-size="20" text-anchor="middle">Name</text>
        <text x="300" y="70" fill="black" font-size="20" text-anchor="middle">Prénom</text>
              
        <text x="300" y="140" fill="black" font-size="20"  text-anchor="middle" >Name</text>
        <text x="300" y="170" fill="black" font-size="20"  text-anchor="middle">Prénom</text>

      </svg> <br/>
      <br/>
      
      
      
<svg height="400" width="900">

        <rect width="200" height="80" x="200" y="10"   style="fill:rgb(230,230,230);stroke-width:2;stroke:black"/>
        <rect width="200" height="80" x="200" y="110"   style="fill:rgb(230,230,230);stroke-width:2;stroke:black"/>
        
        <line x1="400" y1="50" x2="422" y2="50" style="stroke:black;stroke-width:4" />
        <line x1="400" y1="150" x2="422" y2="150" style="stroke:black;stroke-width:4" />
        <line x1="420" y1="50" x2="420" y2="150" style="stroke:black;stroke-width:4" />
        <line x1="420" y1="100" x2="440" y2="100" style="stroke:black;stroke-width:4" />
        <rect width="200" height="80" x="440" y="60"   style="fill:rgb(230,230,230);stroke-width:2;stroke:black"/>
        
	<text x="100" y="40" fill="black" font-size="20" text-anchor="middle">Groupe A</text>
	<text x="100" y="60" fill="black" font-size="20" text-anchor="middle">1er</text>
        <text x="100" y="140" fill="black" font-size="20" text-anchor="middle">Groupe B</text>
        <text x="100" y="160" fill="black" font-size="20" text-anchor="middle">2e</text>
        
        <text x="300" y="40" fill="black" font-size="20" text-anchor="middle">Name</text>
        <text x="300" y="70" fill="black" font-size="20" text-anchor="middle">Prénom</text>
              
        <text x="300" y="140" fill="black" font-size="20"  text-anchor="middle" >Name</text>
        <text x="300" y="170" fill="black" font-size="20"  text-anchor="middle">Prénom</text>
        
        <rect width="200" height="80" x="200" y="210"   style="fill:rgb(230,230,230);stroke-width:2;stroke:black"/>
        <rect width="200" height="80" x="200" y="310"   style="fill:rgb(230,230,230);stroke-width:2;stroke:black"/>
        
        <line x1="400" y1="250" x2="422" y2="250" style="stroke:black;stroke-width:4" />
        <line x1="400" y1="350" x2="422" y2="350" style="stroke:black;stroke-width:4" />
        <line x1="420" y1="250" x2="420" y2="350" style="stroke:black;stroke-width:4" />
        <line x1="420" y1="300" x2="440" y2="300" style="stroke:black;stroke-width:4" />
        <rect width="200" height="80" x="440" y="260"   style="fill:rgb(230,230,230);stroke-width:2;stroke:black"/>
        
	<text x="100" y="240" fill="black" font-size="20" text-anchor="middle">Groupe B</text>
	<text x="100" y="260" fill="black" font-size="20" text-anchor="middle">1er</text>
        <text x="100" y="340" fill="black" font-size="20" text-anchor="middle">Groupe A</text>
        <text x="100" y="360" fill="black" font-size="20" text-anchor="middle">2e</text>
        
        <text x="300" y="240" fill="black" font-size="20" text-anchor="middle">Name</text>
        <text x="300" y="270" fill="black" font-size="20" text-anchor="middle">Prénom</text>
              
        <text x="300" y="340" fill="black" font-size="20"  text-anchor="middle" >Name</text>
        <text x="300" y="370" fill="black" font-size="20"  text-anchor="middle">Prénom</text>

        <line x1="640" y1="100" x2="662" y2="100" style="stroke:black;stroke-width:4" />
        <line x1="640" y1="300" x2="662" y2="300" style="stroke:black;stroke-width:4" />
        <line x1="660" y1="100" x2="660" y2="300" style="stroke:black;stroke-width:4" />
        <line x1="660" y1="200" x2="680" y2="200" style="stroke:black;stroke-width:4" />
        <rect width="200" height="80" x="680" y="160"   style="fill:rgb(230,230,230);stroke-width:2;stroke:black"/>

        
      </svg>





      
    ';
}
plot_pool_3(7,'Cat Name', 'Step Name');
?>
