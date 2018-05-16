<?php

/**
 * Copyright 2016 LINE Corporation
 *
 * LINE Corporation licenses this file to you under the Apache License,
 * version 2.0 (the "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at:
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

require_once('./LINEBotTiny.php');

$channelAccessToken = 'QRFVDH6aSnCCDloCllamdobYsfj+gDmjR/6642WAmnJ9tuRb7e9azRiZaDy9yjOpEJ/gBMJ2JV5cJgrCM/HDA9qkX/4GPqBTdVZkrysJzwxKwH8mgAqeIvaQUp3JTa7W4L1DJkVNOGi3wJMAbIZk4AdB04t89/1O/w1cDnyilFU=';
$channelSecret = '2840625fe0df3410ee2c566ef34638ce';

$SLID = array('2006');

$client = new LINEBotTiny($channelAccessToken, $channelSecret);
foreach ($client->parseEvents() as $event) {
    switch ($event['type']) {
        case 'message':
            $message = $event['message'];
            
            //Modify below
            $dom = new DOMDocument();
            @$dom->loadHTMLFile("http://pda.5284.com.tw/MQS/businfo4.jsp?SLID=".$SLID[0]);
            $trs = $dom->getElementsByTagName('tr');
            $bus_info = "開南商工 往中和\n\n";
            $flag = 0; // This flag is used to ignore some header of info.
            foreach ($trs as $tr) {
                if ($flag <= 3) {
                    $flag += 1;
                    continue;
                }
                $tds = $tr->getElementsByTagName('td');
                if ($tds[0]->nodeValue == "208" || $tds[0]->nodeValue == "671"){
                    $bus_info = $bus_info.$tds[0]->nodeValue."\t".$tds[3]->nodeValue."\n";
                }
            }
            // Modify above

            switch ($message['type']) {
                case 'text':
                    $client->replyMessage(array(
                        'replyToken' => $event['replyToken'],
                        'messages' => array(
                            array(
                                'type' => 'text',
                                'text' => $bus_info//$message['text']
                            )
                        )
                    ));
                    break;
                default:
                    error_log("Unsupporeted message type: " . $message['type']);
                    break;
            }
            break;
        default:
            error_log("Unsupporeted event type: " . $event['type']);
            break;
    }
};
