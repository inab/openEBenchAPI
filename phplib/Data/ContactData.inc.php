<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function getContactInfo() {
    $data['Total'] = $GLOBALS['cols']['Contact']->count();
    $data['lastUpdate'] = getUpdateDate($GLOBALS['cols']['Contact']);
    return $data;
}

function searchContact($params) {
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
    foreach ($GLOBALS['cols']['Contact']->find($fcond, ['sort' => $sortA]) as $rs) {
        $results[] = $rs;
    }
    return $results;
}

function getContactData($id, $extended = false) {

    $data = $GLOBALS['cols']['Contact']->findOne(['_id' => $id]);
    foreach ($GLOBALS['cols']['Community']->find(['community_contacts' => $id],['projection'=>['_id'=>1]]) as $d) {
        $data['CommunityList'][]=$d['_id'];
    }
    $data['LinksList']=[];
    foreach ($data['links'] as $l) {
        $data['LinksList'][] = $l['label'].": ".$l['uri'];
    }
    return $data;
}