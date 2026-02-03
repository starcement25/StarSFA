<?php

try {
					            $url = "http://salesmpower.acedns.in/star_lead_email_new.php";
                                $ch = curl_init($url);
                                curl_setopt($ch, CURLOPT_HEADER, false);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($ch, CURLOPT_POST, true);
                                
                                $data = array(
                                    'su_id' => 'SUE055520241208192532',
                                    'password' => ''
                                );
                                
                                
                                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                                $contents = curl_exec($ch);
                                curl_close($ch);
					        }catch(Exception $e) {
                              
                            }

?>