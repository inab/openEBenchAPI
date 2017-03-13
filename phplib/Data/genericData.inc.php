<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function getGenericInfo($dataStore) {
    $data['Total'] = $GLOBALS['cols'][$dataStore]->count();
    $data['lastUpdate'] = getUpdateDate($GLOBALS['cols'][$dataStore]);
    return $data;
}

function searchGeneric($dataStore,$params) {
    $cond = [];

    if (isset($params['query'])) {
// Text index pendents versio Mongodb
        foreach (explode(' ', $params['query']) as $wd) {
            $cl2 = [];
            foreach (array_keys($params['queryOn']) as $fld) {
                $rex = new MongoDB\BSON\Regex($wd, "i");
                $cl2[] = [$fld => $rex];
            }
            if (count($cl2) > 1) {
                $cond[] = ['$or' => $cl2];
            } else {
                $cond[] = $cl2[0];
            }
        }
    }
    if (count($cond)) {
        $fcond = ['$and' => $cond];
    } else {
        $fcond = [];
    }
//print "<pre>";
//print json_encode($fcond);
//print "</pre>";

    $sortA = [];
    foreach ($GLOBALS['cols'][$dataStore]->find($fcond, ['sort' => $sortA]) as $rs) {
        $results[] = $rs;
    }
    return $results;
}

function getDataGeneric($dataStore,$id) {
    $data = $GLOBALS['cols'][$dataStore]->findOne(['_id' => $id]);
    return $data;
}