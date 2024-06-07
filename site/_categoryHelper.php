<?php

 
function create_step_direct($ActualCategory_id, $name){
    $mysqli= ConnectionFactory::GetConnection(); 
    
    $stmt = $mysqli->prepare("INSERT INTO CategoryStep (ActualCategoryId,CategoryStepsTypeId, Name) VALUES (?,1,?)");
    $stmt->bind_param("is", $ActualCategory_id, $name);         
    $stmt->execute();
    $step_id = $mysqli->insert_id;
    $stmt->close();
    
    
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId,step_id) VALUES (?,?)");
    $stmt->bind_param("ii", $ActualCategory_id, $step_id);         
    $stmt->execute();
    $stmt->close();


    return $step_id;
}

function create_link($ActualCategory_id, $out_step_id, $step_id_1_id, $rank_1, $step_id_2_id, $rank_2){
    $mysqli= ConnectionFactory::GetConnection(); 
    
    $stmt = $mysqli->prepare("INSERT INTO StepLinking (ActualCategoryId, out_step_id, in_step_1_id, rank_in_step_1, in_step_2_id, rank_in_step_2) VALUES (?,?,?,?,?,?)");
    $stmt->bind_param("iiiiii", $ActualCategory_id, $out_step_id, $step_id_1_id, $rank_1, $step_id_2_id, $rank_2);         
    $stmt->execute();
    $step_id = $mysqli->insert_id;
    $stmt->close();
}

function update_step_direct($step_id, $user_id_1, $user_id_2){
    $mysqli= ConnectionFactory::GetConnection(); 
    
    $stmt = $mysqli->prepare("SELECT Id FROM CategoryStep WHERE Id=? AND CategoryStepsTypeId=1");
    $stmt->bind_param("i", $step_id);   
    $stmt->bind_result( $r_step_Id);
    $stmt->execute();
    $stmt->fetch(); 
    $stmt->close();
    
    if (!empty($r_step_Id)){
        $mysqli= ConnectionFactory::GetConnection(); 
        $stmt = $mysqli->prepare("UPDATE Fight SET TournamentCompetitor1Id=?, TournamentCompetitor2Id=? WHERE step_id=?");
        $stmt->bind_param("iii", $user_id_1, $user_id_2, $step_id);         
        $stmt->execute();
        $stmt->close();
    }
}


function create_step_pool_2($ActualCategory_id, $user_id_1, $user_id_2, $name){
    $mysqli= ConnectionFactory::GetConnection(); 
    
    $stmt = $mysqli->prepare("INSERT INTO CategoryStep (ActualCategoryId,CategoryStepsTypeId, Name) VALUES (?,1,?)");
    $stmt->bind_param("is", $ActualCategory_id, $name);         
    $stmt->execute();
    $step_id = $mysqli->insert_id;
    $stmt->close();
    
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_1, $user_id_2);         
    $stmt->execute();
    $stmt->close();

    return $step_id;
}


function create_step_pool_3($ActualCategory_id, $user_id_1, $user_id_2, $user_id_3, $name) {
    $mysqli= ConnectionFactory::GetConnection(); 
    
    $stmt = $mysqli->prepare("INSERT INTO CategoryStep (ActualCategoryId,CategoryStepsTypeId, Name) VALUES (?,3,?)");
    $stmt->bind_param("is", $ActualCategory_id, $name);           
    $stmt->execute();
    $step_id = $mysqli->insert_id;
    $stmt->close();
    
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_1, $user_id_2);         
    $stmt->execute();
    $stmt->close();
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_1, $user_id_3);         
    $stmt->execute();
    $stmt->close();
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_2, $user_id_3);         
    $stmt->execute();
    $stmt->close();

    return $step_id;
}

function create_step_pool_4($ActualCategory_id, $user_id_1, $user_id_2, $user_id_3, $user_id_4, $name) {
    $mysqli= ConnectionFactory::GetConnection(); 
    
    $stmt = $mysqli->prepare("INSERT INTO CategoryStep (ActualCategoryId,CategoryStepsTypeId, Name) VALUES (?,4,?)");
    $stmt->bind_param("is", $ActualCategory_id, $name);           
    $stmt->execute();
    $step_id = $mysqli->insert_id;
    $stmt->close();
    
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_1, $user_id_2);         
    $stmt->execute();
    $stmt->close();
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_3, $user_id_4);         
    $stmt->execute();
    $stmt->close();
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_2, $user_id_4);         
    $stmt->execute();
    $stmt->close();
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_1, $user_id_3);         
    $stmt->execute();
    $stmt->close();
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_1, $user_id_4);         
    $stmt->execute();
    $stmt->close();
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_2, $user_id_3);         
    $stmt->execute();
    $stmt->close();
    

    return $step_id;
}

function create_step_pool_5($ActualCategory_id, $user_id_1, $user_id_2, $user_id_3, $user_id_4, $user_id_5, $name) {
    $mysqli= ConnectionFactory::GetConnection(); 
    
    $stmt = $mysqli->prepare("INSERT INTO CategoryStep (ActualCategoryId,CategoryStepsTypeId, Name) VALUES (?,5,?)");
    $stmt->bind_param("is", $ActualCategory_id, $name);         
    $stmt->execute();
    $step_id = $mysqli->insert_id;
    $stmt->close();
    
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_1, $user_id_2);         
    $stmt->execute();
    $stmt->close();
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_3, $user_id_4);         
    $stmt->execute();
    $stmt->close();
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_1, $user_id_5);         
    $stmt->execute();
    $stmt->close();
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_2, $user_id_4);         
    $stmt->execute();
    $stmt->close();
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_3, $user_id_5);         
    $stmt->execute();
    $stmt->close();
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_1, $user_id_4);         
    $stmt->execute();
    $stmt->close();
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_2, $user_id_5);         
    $stmt->execute();
    $stmt->close();
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_1, $user_id_3);         
    $stmt->execute();
    $stmt->close();   
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_4, $user_id_5);         
    $stmt->execute();
    $stmt->close();
    $stmt = $mysqli->prepare("INSERT INTO Fight (ActualCategoryId, step_id, TournamentCompetitor1Id, TournamentCompetitor2Id) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $ActualCategory_id, $step_id, $user_id_2, $user_id_3);         
    $stmt->execute();
    $stmt->close();
    
    return $step_id;
}

function create_steps_6($ActualCategoryId, $user_id_1, $user_id_2, $user_id_3, $user_id_4, $user_id_5, $user_id_6) {
    $pool_1=create_step_pool_3($ActualCategoryId, $user_id_1, $user_id_3, $user_id_5, 'Groupe A');
    $pool_2=create_step_pool_3($ActualCategoryId, $user_id_2, $user_id_4, $user_id_6, 'Groupe B');
    
    $half_1=create_step_direct($ActualCategoryId,'Demi-Finale 1');
    create_link($ActualCategoryId, $half_1, $pool_1, 1, $pool_2, 2);
    
    $half_2=create_step_direct($ActualCategoryId,'Demi-Finale 2');
    create_link($ActualCategoryId, $half_2, $pool_1, 2, $pool_2, 1);
    
    $final=create_step_direct($ActualCategoryId,'Finale');
    create_link($ActualCategoryId, $final, $half_1, 1, $half_2, 1);
}

function create_steps_7($ActualCategoryId, $user_id_1, $user_id_2, $user_id_3, $user_id_4, $user_id_5, $user_id_6, $user_id_7) {
    $pool_1=create_step_pool_4($ActualCategoryId, $user_id_1, $user_id_3, $user_id_5, $user_id_7, 'Groupe A');
    $pool_2=create_step_pool_3($ActualCategoryId, $user_id_2, $user_id_4, $user_id_6, 'Groupe B');
    
    $half_1=create_step_direct($ActualCategoryId,'Demi-Finale 1');
    create_link($ActualCategoryId, $half_1, $pool_1, 1, $pool_2, 2);
    
    $half_2=create_step_direct($ActualCategoryId,'Demi-Finale 2');
    create_link($ActualCategoryId, $half_2, $pool_1, 2, $pool_2, 1);
    
    $final=create_step_direct($ActualCategoryId,'Finale');
    create_link($ActualCategoryId, $final, $half_1, 1, $half_2, 1);
}

function create_steps_8($ActualCategoryId, $user_id_1, $user_id_2, $user_id_3, $user_id_4, $user_id_5, $user_id_6, $user_id_7, $user_id_8) {
    $pool_1=create_step_pool_4($ActualCategoryId, $user_id_1, $user_id_3, $user_id_5, $user_id_7, 'Groupe A');
    $pool_2=create_step_pool_4($ActualCategoryId, $user_id_2, $user_id_4, $user_id_6, $user_id_8, 'Groupe B');
    
    $half_1=create_step_direct($ActualCategoryId,'Demi-Finale 1');
    create_link($ActualCategoryId, $half_1, $pool_1, 1, $pool_2, 2);
    
    $half_2=create_step_direct($ActualCategoryId,'Demi-Finale 2');
    create_link($ActualCategoryId, $half_2, $pool_1, 2, $pool_2, 1);
    
    $final=create_step_direct($ActualCategoryId,'Finale');
    create_link($ActualCategoryId, $final, $half_1, 1, $half_2, 1);
}

function create_steps_9($ActualCategoryId, $user_id_1, $user_id_2, $user_id_3, $user_id_4, $user_id_5, $user_id_6, $user_id_7, $user_id_8, $user_id_9) {
    $pool_1=create_step_pool_4($ActualCategoryId, $user_id_1, $user_id_3, $user_id_5, $user_id_7, $user_id_9, 'Groupe A');
    $pool_2=create_step_pool_5($ActualCategoryId, $user_id_2, $user_id_4, $user_id_6, $user_id_8, 'Groupe B');
    
    $half_1=create_step_direct($ActualCategoryId,'Demi-Finale 1');
    create_link($ActualCategoryId, $half_1, $pool_1, 1, $pool_2, 2);
    
    $half_2=create_step_direct($ActualCategoryId,'Demi-Finale 2');
    create_link($ActualCategoryId, $half_2, $pool_1, 2, $pool_2, 1);
    
    $final=create_step_direct($ActualCategoryId,'Finale');
    create_link($ActualCategoryId, $final, $half_1, 1, $half_2, 1);
}

function create_steps_10($ActualCategoryId, $user_id_1, $user_id_2, $user_id_3, $user_id_4, $user_id_5, $user_id_6, $user_id_7, $user_id_8, $user_id_9, $user_id_10) {
    $pool_1=create_step_pool_5($ActualCategoryId, $user_id_1, $user_id_3, $user_id_5, $user_id_7, $user_id_9, 'Groupe A');
    $pool_2=create_step_pool_5($ActualCategoryId, $user_id_2, $user_id_4, $user_id_6, $user_id_8, $user_id_10, 'Groupe B');
    
    $half_1=create_step_direct($ActualCategoryId,'Demi-Finale 1');
    create_link($ActualCategoryId, $half_1, $pool_1, 1, $pool_2, 2);
    
    $half_2=create_step_direct($ActualCategoryId,'Demi-Finale 2');
    create_link($ActualCategoryId, $half_2, $pool_1, 2, $pool_2, 1);
    
    $final=create_step_direct($ActualCategoryId,'Finale');
    create_link($ActualCategoryId, $final, $half_1, 1, $half_2, 1);
}

function create_steps_11($ActualCategoryId, $user_ids) {
    $pool_1=create_step_pool_4($ActualCategoryId, $user_ids[0], $user_ids[3], $user_ids[6], $user_ids[9], 'Groupe A');
    $pool_2=create_step_pool_4($ActualCategoryId, $user_ids[1], $user_ids[4], $user_ids[7], $user_ids[10], 'Groupe B');
    $pool_3=create_step_pool_3($ActualCategoryId, $user_ids[2], $user_ids[5], $user_ids[8], 'Groupe C');

    $half_1=create_step_direct($ActualCategoryId,'Demi-Finale 1');
    create_link($ActualCategoryId, $half_1, $pool_1, 1, $pool_3, 1);
    
    $final=create_step_direct($ActualCategoryId,'Finale');
    create_link($ActualCategoryId, $final, $half_1, 1, $pool_2, 1);
}

function create_steps_12($ActualCategoryId, $user_ids) {
    $pool_1=create_step_pool_3($ActualCategoryId, $user_ids[0], $user_ids[4], $user_ids[8], 'Groupe A');
    $pool_2=create_step_pool_3($ActualCategoryId, $user_ids[1], $user_ids[5], $user_ids[9], 'Groupe B');
    $pool_3=create_step_pool_3($ActualCategoryId, $user_ids[2], $user_ids[6], $user_ids[10], 'Groupe C');
    $pool_4=create_step_pool_3($ActualCategoryId, $user_ids[3], $user_ids[7], $user_ids[11], 'Groupe D');

    $half_1=create_step_direct($ActualCategoryId,'Demi-Finale 1');
    create_link($ActualCategoryId, $half_1, $pool_1, 1, $pool_3, 1);
    
    $half_2=create_step_direct($ActualCategoryId,'Demi-Finale 2');
    create_link($ActualCategoryId, $half_2, $pool_2, 1, $pool_4, 1);
    
    $final=create_step_direct($ActualCategoryId,'Finale');
    create_link($ActualCategoryId, $final, $half_1, 1, $half_2, 1);
}

function create_steps_13($ActualCategoryId, $user_ids) {
    $pool_1=create_step_pool_4($ActualCategoryId, $user_ids[0], $user_ids[4], $user_ids[8], $user_ids[12], 'Groupe A');
    $pool_2=create_step_pool_3($ActualCategoryId, $user_ids[1], $user_ids[5], $user_ids[9], 'Groupe B');
    $pool_3=create_step_pool_3($ActualCategoryId, $user_ids[2], $user_ids[6], $user_ids[10], 'Groupe C');
    $pool_4=create_step_pool_3($ActualCategoryId, $user_ids[3], $user_ids[7], $user_ids[11], 'Groupe D');
    
    $half_1=create_step_direct($ActualCategoryId,'Demi-Finale 1');
    create_link($ActualCategoryId, $half_1, $pool_1, 1, $pool_3, 1);
    
    $half_2=create_step_direct($ActualCategoryId,'Demi-Finale 2');
    create_link($ActualCategoryId, $half_2, $pool_2, 1, $pool_4, 1);
    
    $final=create_step_direct($ActualCategoryId,'Finale');
    create_link($ActualCategoryId, $final, $half_1, 1, $half_2, 1);
}

function create_steps_14($ActualCategoryId, $user_ids) {
    $pool_1=create_step_pool_4($ActualCategoryId, $user_ids[0], $user_ids[4], $user_ids[8], $user_ids[12], 'Groupe A');
    $pool_2=create_step_pool_4($ActualCategoryId, $user_ids[1], $user_ids[5], $user_ids[9], $user_ids[13], 'Groupe B');
    $pool_3=create_step_pool_3($ActualCategoryId, $user_ids[2], $user_ids[6], $user_ids[10], 'Groupe C');
    $pool_4=create_step_pool_3($ActualCategoryId, $user_ids[3], $user_ids[7], $user_ids[11], 'Groupe D');
    
    $half_1=create_step_direct($ActualCategoryId,'Demi-Finale 1');
    create_link($ActualCategoryId, $half_1, $pool_1, 1, $pool_3, 1);
    
    $half_2=create_step_direct($ActualCategoryId,'Demi-Finale 2');
    create_link($ActualCategoryId, $half_2, $pool_2, 1, $pool_4, 1);
    
    $final=create_step_direct($ActualCategoryId,'Finale');
    create_link($ActualCategoryId, $final, $half_1, 1, $half_2, 1);
}

function create_steps_15($ActualCategoryId, $user_ids) {
    $pool_1=create_step_pool_4($ActualCategoryId, $user_ids[0], $user_ids[4], $user_ids[8], $user_ids[12], 'Groupe A');
    $pool_2=create_step_pool_4($ActualCategoryId, $user_ids[1], $user_ids[5], $user_ids[9], $user_ids[13], 'Groupe B');
    $pool_3=create_step_pool_4($ActualCategoryId, $user_ids[2], $user_ids[6], $user_ids[10], $user_ids[14], 'Groupe C');
    $pool_4=create_step_pool_3($ActualCategoryId, $user_ids[3], $user_ids[7], $user_ids[11], 'Groupe C');
    
    $half_1=create_step_direct($ActualCategoryId,'Demi-Finale 1');
    create_link($ActualCategoryId, $half_1, $pool_1, 1, $pool_3, 1);
    
    $half_2=create_step_direct($ActualCategoryId,'Demi-Finale 2');
    create_link($ActualCategoryId, $half_2, $pool_2, 1, $pool_4, 1);
    
    $final=create_step_direct($ActualCategoryId,'Finale');
    create_link($ActualCategoryId, $final, $half_1, 1, $half_2, 1);
}

function create_steps_16($ActualCategoryId, $user_ids) {
    $pool_1=create_step_pool_4($ActualCategoryId, $user_ids[0], $user_ids[4], $user_ids[8], $user_ids[12], 'Groupe A');
    $pool_2=create_step_pool_4($ActualCategoryId, $user_ids[1], $user_ids[5], $user_ids[9], $user_ids[13], 'Groupe B');
    $pool_3=create_step_pool_4($ActualCategoryId, $user_ids[2], $user_ids[6], $user_ids[10], $user_ids[14], 'Groupe C');
    $pool_4=create_step_pool_4($ActualCategoryId, $user_ids[3], $user_ids[7], $user_ids[11], $user_ids[15], 'Groupe D');

    $half_1=create_step_direct($ActualCategoryId,'Demi-Finale 1');
    create_link($ActualCategoryId, $half_1, $pool_1, 1, $pool_3, 1);
    
    $half_2=create_step_direct($ActualCategoryId,'Demi-Finale 2');
    create_link($ActualCategoryId, $half_2, $pool_2, 1, $pool_4, 1);
    
    $final=create_step_direct($ActualCategoryId,'Finale');
    create_link($ActualCategoryId, $final, $half_1, 1, $half_2, 1);
}

function create_steps_17($ActualCategoryId, $user_ids) {
    $pool_1=create_step_pool_5($ActualCategoryId, $user_ids[0], $user_ids[4], $user_ids[8], $user_ids[12], $user_ids[16], 'Groupe A');
    $pool_2=create_step_pool_4($ActualCategoryId, $user_ids[1], $user_ids[5], $user_ids[9], $user_ids[13], 'Groupe B');
    $pool_3=create_step_pool_4($ActualCategoryId, $user_ids[2], $user_ids[6], $user_ids[10], $user_ids[14], 'Groupe C');
    $pool_4=create_step_pool_4($ActualCategoryId, $user_ids[3], $user_ids[7], $user_ids[11], $user_ids[15], 'Groupe D');

    $half_1=create_step_direct($ActualCategoryId,'Demi-Finale 1');
    create_link($ActualCategoryId, $half_1, $pool_1, 1, $pool_3, 1);
    
    $half_2=create_step_direct($ActualCategoryId,'Demi-Finale 2');
    create_link($ActualCategoryId, $half_2, $pool_2, 1, $pool_4, 1);
    
    $final=create_step_direct($ActualCategoryId,'Finale');
    create_link($ActualCategoryId, $final, $half_1, 1, $half_2, 1);
}

function create_steps_18($ActualCategoryId, $user_ids) {
    $pool_1=create_step_pool_5($ActualCategoryId, $user_ids[0], $user_ids[4], $user_ids[8], $user_ids[12], $user_ids[16], 'Groupe A');
    $pool_2=create_step_pool_5($ActualCategoryId, $user_ids[1], $user_ids[5], $user_ids[9], $user_ids[13], $user_ids[17], 'Groupe B');
    $pool_3=create_step_pool_4($ActualCategoryId, $user_ids[2], $user_ids[6], $user_ids[10], $user_ids[14], 'Groupe C');
    $pool_4=create_step_pool_4($ActualCategoryId, $user_ids[3], $user_ids[7], $user_ids[11], $user_ids[15], 'Groupe D');

    $half_1=create_step_direct($ActualCategoryId,'Demi-Finale 1');
    create_link($ActualCategoryId, $half_1, $pool_1, 1, $pool_3, 1);
    
    $half_2=create_step_direct($ActualCategoryId,'Demi-Finale 2');
    create_link($ActualCategoryId, $half_2, $pool_2, 1, $pool_4, 1);
    
    $final=create_step_direct($ActualCategoryId,'Finale');
    create_link($ActualCategoryId, $final, $half_1, 1, $half_2, 1);
}

function create_steps_19($ActualCategoryId, $user_ids) {
    $pool_1=create_step_pool_5($ActualCategoryId, $user_ids[0], $user_ids[4], $user_ids[8], $user_ids[12], $user_ids[16], 'Groupe A');
    $pool_2=create_step_pool_5($ActualCategoryId, $user_ids[1], $user_ids[5], $user_ids[9], $user_ids[13], $user_ids[17], 'Groupe B');
    $pool_3=create_step_pool_5($ActualCategoryId, $user_ids[2], $user_ids[6], $user_ids[10], $user_ids[14], $user_ids[18], 'Groupe C');
    $pool_4=create_step_pool_4($ActualCategoryId, $user_ids[3], $user_ids[7], $user_ids[11], $user_ids[15], 'Groupe D');

    $half_1=create_step_direct($ActualCategoryId,'Demi-Finale 1');
    create_link($ActualCategoryId, $half_1, $pool_1, 1, $pool_3, 1);
    
    $half_2=create_step_direct($ActualCategoryId,'Demi-Finale 2');
    create_link($ActualCategoryId, $half_2, $pool_2, 1, $pool_4, 1);
    
    $final=create_step_direct($ActualCategoryId,'Finale');
    create_link($ActualCategoryId, $final, $half_1, 1, $half_2, 1);
}

function create_steps_20($ActualCategoryId, $user_ids) {
    $pool_1=create_step_pool_5($ActualCategoryId, $user_ids[0], $user_ids[4], $user_ids[8], $user_ids[12], $user_ids[16], 'Groupe A');
    $pool_2=create_step_pool_5($ActualCategoryId, $user_ids[1], $user_ids[5], $user_ids[9], $user_ids[13], $user_ids[17], 'Groupe B');
    $pool_3=create_step_pool_5($ActualCategoryId, $user_ids[2], $user_ids[6], $user_ids[10], $user_ids[14], $user_ids[18], 'Groupe C');
    $pool_4=create_step_pool_5($ActualCategoryId, $user_ids[3], $user_ids[7], $user_ids[11], $user_ids[15], $user_ids[19], 'Groupe D');

    $half_1=create_step_direct($ActualCategoryId,'Demi-Finale 1');
    create_link($ActualCategoryId, $half_1, $pool_1, 1, $pool_3, 1);
    
    $half_2=create_step_direct($ActualCategoryId,'Demi-Finale 2');
    create_link($ActualCategoryId, $half_2, $pool_2, 1, $pool_4, 1);
    
    $final=create_step_direct($ActualCategoryId,'Finale');
    create_link($ActualCategoryId, $final, $half_1, 1, $half_2, 1);
}


function get_list_from_val($exp_val,$dict){
    $res=array();
    foreach ($dict as $key => $val) {
        if ($val==$exp_val){
             array_push($res, $key);
        }
    }
    return $res;
}

function get_step_results($step_id){
    $mysqli= ConnectionFactory::GetConnection(); 
    
    $stmt = $mysqli->prepare("SELECT CategoryStepsTypeId FROM CategoryStep WHERE Id=?");
    $stmt->bind_param("i", $step_id);         
    $stmt->bind_result($step_type);     
    $stmt->execute();  
    $stmt->fetch();
    $stmt->close();
    
    if ($step_type==1) {
        //direct step 
        $stmt = $mysqli->prepare("SELECT TournamentCompetitor1Id,pv1,TournamentCompetitor2Id,pv2 FROM Fight WHERE step_id=?");
        $stmt->bind_param("i", $step_id);         
        $stmt->bind_result($TournamentCompetitor1Id, $pv1, $TournamentCompetitor2Id, $pv2);     
        $stmt->execute();  
        $stmt->fetch();
        $stmt->close();
        if ($pv1>0){
                return array(1=>$TournamentCompetitor1Id, 2=>$TournamentCompetitor2Id);
        } else if ($pv2>0){
                return array(1=>$TournamentCompetitor2Id, 2=>$TournamentCompetitor1Id);
        } else {
            return NULL;
        }
    } else {
        // pool step  
        $stmt = $mysqli->prepare("SELECT TournamentCompetitor1Id,pv1,TournamentCompetitor2Id,pv2 FROM Fight WHERE step_id=?");
        $stmt->bind_param("i", $step_id);         
        $stmt->bind_result($TournamentCompetitor1Id, $pv1, $TournamentCompetitor2Id, $pv2);     
        $stmt->execute();  
        $results = array();
        // victory and PV
        while ($stmt->fetch()) {
            if (empty($pv1) && empty($pv2)){
                return NULL;
            }
            
            if (!array_key_exists($TournamentCompetitor1Id, $results)){
                $results[$TournamentCompetitor1Id]=0;
            }
            
            if (!array_key_exists($TournamentCompetitor2Id, $results)){
                $results[$TournamentCompetitor2Id]=0;
            }
            
            $results[$TournamentCompetitor1Id] += 10000*(int)($pv1>0)+10*$pv1;
            $results[$TournamentCompetitor2Id] += 10000*(int)($pv2>0)+10*$pv2;
        }
        $stmt->close();
        
        
        // Direct fight in case of equality
        $counts = array_count_values($results);
        foreach ($counts as $value => $mult) {   
            $tied_keys=get_list_from_val($value,$results);
            $stmt = $mysqli->prepare("SELECT TournamentCompetitor1Id, pv1, TournamentCompetitor2Id FROM Fight WHERE step_id=? AND TournamentCompetitor1Id IN (".implode(',',$tied_keys).") AND TournamentCompetitor2Id IN (".implode(',',$tied_keys).")");
            $stmt->bind_param("i", $step_id);         
            $stmt->bind_result($TCompetitor1Id,$pv1,$TCompetitor2Id);     
            $stmt->execute();  
            while ($stmt->fetch()) {
                $results[$TCompetitor1Id] += (int)($pv1>0);
                $results[$TCompetitor2Id] += (int)($pv1==0);
            }
            $stmt->close(); 
        }
        
        // TODO tie
        if(max(array_count_values($results))>1){
            echo 'ERROR IN the step: tied results';
           
            echo 'in pool step with id='.$step_id.' tie';
            exit;
        }
        
        arsort($results, SORT_NUMERIC);

        $res= array();
        $idx=1;
        foreach ($results as $cmp => $point) {
            $res[$idx]=$cmp;
            $idx+=1;
        }
        
        return $res;
    }
}





function check_link($ActualCategoryId) {
    $mysqli= ConnectionFactory::GetConnection(); 
    
    $stmt = $mysqli->prepare("SELECT out_step_id, in_step_1_id, rank_in_step_1, in_step_2_id, rank_in_step_2 FROM StepLinking WHERE ActualCategoryId=?");
    $stmt->bind_param("i", $ActualCategoryId);         
    $stmt->bind_result($out_step,$in_step_1,$rank_1,$in_step_2,$rank_2);     
    $stmt->execute();  
    $links=array();
    while ($stmt->fetch()){
        $res_1 = get_step_results($in_step_1);
        $res_2 = get_step_results($in_step_2);
        if (!empty($res_1) and !empty($res_2)) {
            $to_update = array("step_Id"=>$out_step, 
                          "f1"=>$res_1[$rank_1], 
                          "f2"=>$res_2[$rank_2]);
            $links[]=$to_update;
        }
    }
    $stmt->close();
    
    foreach ($links as $link){
        update_step_direct($link["step_Id"],$link["f1"],$link["f2"]);
    }
}

function add_fight_result($ActualCategoryId, $fight_id, $pv_1, $pv_2){
   
    
    if ($pv_1>=0 && $pv_2>=0 && $pv_1*$pv_2==0 && $pv_1+$pv_2>0) {
    
 
        $mysqli= ConnectionFactory::GetConnection(); 
        
        $stmt = $mysqli->prepare("UPDATE Fight SET pv1=?, pv2=? WHERE Id=?");
        $stmt->bind_param("iii", $pv_1, $pv_2, $fight_id);       
        $stmt->execute();
        $stmt->close();
        
        check_link($ActualCategoryId);
        if (isCatCompleted($ActualCategoryId)) {
            close_category($ActualCategoryId);
        }
    } else {echo 'Error in data'; exit;}
    
}

function isCatCompleted($ActualCategoryId){
    $mysqli= ConnectionFactory::GetConnection(); 
    
    // look for linking and more specifically for the step which is only in the output never in the inputs
    $stmt = $mysqli->prepare("SELECT count(Id) 
                              FROM Fight 
                              WHERE ActualCategoryId =? AND pv1 IS NULL");
    $stmt->bind_param("i", $ActualCategoryId);         
    $stmt->bind_result($missing_fight);     
    $stmt->execute();  
    $stmt->fetch();
    $stmt->close();
    return empty($missing_fight);
}

function get_full_result($ActualCategoryId){
    if (!isCatCompleted($ActualCategoryId)){
    
        return NULL;
    }


    $mysqli= ConnectionFactory::GetConnection(); 
    
    // look for linking and more specifically for the step which is only in the output never in the inputs
    $stmt = $mysqli->prepare("SELECT 
                              ST1.out_step_id
                              FROM StepLinking AS ST1
                              LEFT OUTER JOIN StepLinking AS ST2 ON ST1.ActualCategoryId = ST2.ActualCategoryId AND ST1.out_step_id=ST2.in_step_1_id
                              LEFT OUTER JOIN StepLinking AS ST3 ON ST1.ActualCategoryId = ST3.ActualCategoryId AND ST1.out_step_id=ST3.in_step_2_id
                              WHERE ST2.ActualCategoryId IS NULL AND ST3.ActualCategoryId IS NULL AND ST1.ActualCategoryId =?");
    $stmt->bind_param("i", $ActualCategoryId);         
    $stmt->bind_result($final_step_id);     
    $stmt->execute();  
    $stmt->fetch();
    $stmt->close();
    
    if (empty($final_step_id)){
        $stmt = $mysqli->prepare("SELECT Id FROM CategoryStep WHERE ActualCategoryId=?");
        $stmt->bind_param("i", $ActualCategoryId);         
        $stmt->bind_result($step_id);     
        $stmt->execute();  
        $stmt->fetch();
        $stmt->close();
        return get_step_results($step_id);
    } else {
        $result=array();
        $counter=0;
        $current_steps=array($final_step_id);
        $alredy_in=array();
              
        while (count($current_steps)){
            $new_current_steps=array();
            foreach($current_steps as $index=>$stp_id) {
                $step_result = get_step_results($stp_id);
               
                foreach($step_result as $rank=>$comp_id){
                    if (!in_array($comp_id,$alredy_in)) {
                        array_push($alredy_in, $comp_id);
                        
                        if (!array_key_exists($rank+$counter,$result)){
                            $result[$rank+$counter]=array();
                        }
                        
                        array_push($result[$rank+$counter], $comp_id);
                    }
                }
                
                // GET the parent steps from Links
                 $stmt = $mysqli->prepare("SELECT ST1.in_step_1_id, ST1.in_step_2_id 
                                           FROM StepLinking AS ST1
                                           WHERE ST1.out_step_id =?");
                $stmt->bind_param("i", $stp_id);         
                $stmt->bind_result($in_st_id_1,$in_st_id_2);     
                $stmt->execute();  
                while($stmt->fetch()) {
                    array_push($new_current_steps, $in_st_id_1);
                    array_push($new_current_steps, $in_st_id_2);
                }
                $stmt->close();
            }
            
            $counter+=1;
            $current_steps=$new_current_steps;
        }
        
        return $result; 
    }  
}

function open_Category($tc_id_1,$tc_id_2,$name){
    $mysqli= ConnectionFactory::GetConnection(); 
    $actual_category_id = -1;
    if ($tc_id_2>0){
        $stmt = $mysqli->prepare("INSERT INTO ActualCategory (CategoryId,Category2Id,Name) VALUES (?,?,?)");
        $stmt->bind_param("iis", $tc_id_1, $tc_id_2, $name);         
        $stmt->execute();
        $actual_category_id = $mysqli->insert_id;
        $stmt->close();
    } else {
        $stmt = $mysqli->prepare("INSERT INTO ActualCategory (CategoryId,Name) VALUES (?,?)");
        $stmt->bind_param("is", $tc_id_1, $name);         
        $stmt->execute();
        $actual_category_id = $mysqli->insert_id;
        $stmt->close();
    }
    
    
    // get competitor list
    $stmt = $mysqli->prepare("SELECT 
            TournamentCompetitor.Id,
            TournamentCompetitor.ClubId
        FROM TournamentRegistration
        INNER JOIN TournamentCompetitor ON CompetitorId=TournamentCompetitor.Id
        WHERE WeightChecked=1 and TournamentRegistration.CategoryId In (?,?)");
    $stmt->bind_param("ii", $tc_id_1, $tc_id_2);         
    $stmt->bind_result($cmpid,$clubId);     
    $stmt->execute();  
    $user=array();
    while($stmt->fetch()) {
      $user[$cmpid]=$clubId;
    }
    $stmt->close();
    
    // scramble it (scramble the club then user into the club so we avoid people in the same club to be in the same pool)
    $club_list = array_unique(array_values($user));
    $club_val=array();
    
   foreach($club_list as $club_id){
       $club_val[$club_id]=rand(1, count($club_list));
   }
   
   $new_usr=array();
   foreach($user as $uid=>$club_id){
        $new_usr[$uid]= $club_val[$club_id]*100 +rand(1,99);
   }
   
   asort($new_usr, SORT_NUMERIC);
   
   $usr_ids = array_keys($new_usr);
   switch (count($usr_ids)) {
    case 2:
        create_step_pool_2($actual_category_id, $usr_ids[0], $usr_ids[1], 'Finale');
        break;
    case 3:
        create_step_pool_3($actual_category_id, $usr_ids[0], $usr_ids[1], $usr_ids[2],'Groupe A');
        break;
    case 4:
        create_step_pool_4($actual_category_id, $usr_ids[0], $usr_ids[1], $usr_ids[2], $usr_ids[3],'Groupe A');
        break;
    case 5:
        create_step_pool_5($actual_category_id, $usr_ids[0], $usr_ids[1], $usr_ids[2], $usr_ids[3],  $usr_ids[4],'Groupe A');
        break;
    case 6:
        create_steps_6($actual_category_id, $usr_ids[0], $usr_ids[1], $usr_ids[2], $usr_ids[3],  $usr_ids[4],  $usr_ids[5]);
        break;
    case 7:
        create_steps_7($actual_category_id, $usr_ids[0], $usr_ids[1], $usr_ids[2], $usr_ids[3],  $usr_ids[4],  $usr_ids[5],  $usr_ids[6]);
        break;
    case 8:
        create_steps_8($actual_category_id, $usr_ids[0], $usr_ids[1], $usr_ids[2], $usr_ids[3],  $usr_ids[4],  $usr_ids[5],  $usr_ids[6],  $usr_ids[7]);
        break;
    case 9:
        create_steps_9($actual_category_id, $usr_ids[0], $usr_ids[1], $usr_ids[2], $usr_ids[3],  $usr_ids[4],  $usr_ids[5],  $usr_ids[6],  $usr_ids[7],  $usr_ids[8]);
        break;
    case 10:
        create_steps_10($actual_category_id, $usr_ids[0], $usr_ids[1], $usr_ids[2], $usr_ids[3],  $usr_ids[4],  $usr_ids[5],  $usr_ids[6],  $usr_ids[7],  $usr_ids[8],  $usr_ids[9]);
        break;
     //////   
    case 11:
        create_steps_11($actual_category_id, $usr_ids);
        break;
    case 12:
        create_steps_12($actual_category_id, $usr_ids);
        break;
    case 13:
        create_steps_13($actual_category_id, $usr_ids);
        break;
    case 14:
        create_steps_14($actual_category_id, $usr_ids);
        break;
    case 15:
        create_steps_15($actual_category_id, $usr_ids);
        break;
    case 16:
        create_steps_16($actual_category_id, $usr_ids);
        break;
    case 17:
        create_steps_17($actual_category_id, $usr_ids);
        break;
    case 18:
        create_steps_18($actual_category_id, $usr_ids);
        break;
    case 19:
        create_steps_19($actual_category_id, $usr_ids);
        break;
    case 20:
        create_steps_20($actual_category_id, $usr_ids);
        break;

    }
}



function close_category($ActualCategoryId){
    $mysqli= ConnectionFactory::GetConnection(); 
    $result = get_full_result($ActualCategoryId);
    if (! empty($result)){
        foreach($result as $rank=>$cid){
            if (!is_array($cid)) {
                $cid=array($cid);    
            }
            foreach($cid as $compid){
                $stmt = $mysqli->prepare("INSERT INTO ActualCategoryResult (ActualCategoryId,Competitor1Id,RankId) VALUES (?,?,?)");
                $stmt->bind_param("iii", $ActualCategoryId, $compid, $rank);         
                $stmt->execute();
                $stmt->close();
            }
        }
        
         $stmt = $mysqli->prepare("UPDATE ActualCategory SET IsCompleted=1 WHERE Id=?");
         $stmt->bind_param("i", $ActualCategoryId);         
         $stmt->execute();
         $stmt->close();
         
    } else {
       echo 'Cannot close a category with is not completed';
       exit;
    }
}

function order_fight($f_keys){
    $steps=array();
    foreach($f_keys as $k) {
        $sk = explode('-',$k)[0];
        if (!array_key_exists($sk, $steps)){
           $steps[$sk]=array(); 
        }
        $steps[$sk][count($steps[$sk])]=$k;
    }
    
    if (count($steps)==1) {
        return $f_keys;
    } else {
        $max =0;
        foreach ($steps as $s_id=>$s_fgt){
            if ($s_id>0) {
               $max=max($max, count($s_fgt));
            }
        }
        
        $new_list=array();
        
        for($idx=0;$idx<$max;$idx++){
            foreach ($steps as $s_id=>$s_fgt){
                if ($s_id>0) {
                    if ($idx<count($s_fgt)){
                         array_push($new_list,$s_fgt[$idx]);
                    }
                }
            }
        }
        
        foreach ($steps[0] as $fgt){
             array_push($new_list,$fgt);
        } 
        
       return  $new_list;  
        
   } 
}
        
  

?>
