<?php
/**
 * Bytbil Mod - Helper Class
 * 
 * @copyright 	(C) Copyright 2010 SoftPower Technology AB
 * @Module 		Bytbil Mod
 * @license		GNU/GPL
 * Any questions regarding this module shall be directed to:
 * stefan@softpower.se.
 * 
 * This piece of software is released "as is" and comes with no waranties;
 * use and modify it at your own discression. If you have any concern regarding the 
 * quality of the code or its security; please let me know.
 */
 
class bytbilHelper
{
    /**
     * Retrives functionality for the mod
     *
     * @param array $params An object containing the module parameters
     * @access public
     */
    
    //Check if there is a link put into the administrator panel field
    //return the xml pointer if its otherwise print error message
    function initialize($params)
    {
        $file = $params->get('bytbil_link');
            
        if (empty($file))
        {
            echo "Det finns inget att visa. Länken i administrationsdelen kan behövas fyllas i.";
            return 0;
        }
        
        
        else
        {
            $ch = curl_init($file);
            $fp = fopen("temp.html", "w");
    
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_exec($ch);
            curl_close($ch);
            fclose($fp);
            $xml = simplexml_load_file('temp.html');
            return $xml;
        }
    }
    
    //Executes the list mode
    function listMode($xml, $defaultURL)
    {
        //roll through the xml and pick apropreate nodes
        foreach($xml->children() as $node)
        {
            $year = $node->yearmodel;
            $year = substr($year, -2);
            $miles = $node->miles;
            $color = $node->color;
            
            //check for null values and replace output for a tidier look
            if ($miles == '')
                $miles = NULL;
            else
                $miles = $miles.' mil';
                
            if ($color == '')
                $color = NULL;
            else
                $color = $color.', ';
            
            //html output
            echo '<div id="listwrap">';
            echo '<div id="listimagewrap">';
            //node[X] refers to nodes with similar names going from up to down starting with 0
            echo '<a href="'.$defaultURL.'&bytbilid='.$node->id.'"><img id="listimage" width="230" height="153" src="'.$node->images->image[0].'" /></a>';
            echo '</div>';
            echo '<div id="listtextwrap">';
            echo '<a href="'.$defaultURL.'&bytbilid='.$node->id.'"><h3>'.$node->brand .' '. $node->model .' -'.$year;
            echo '</h3>';
            echo '<h5 id="pusher">'.$color.$miles.'&nbsp;</h5>';
            echo '<h3>Pris: '.$node->{'price-sek'}.'kr</h3>';
            echo '</a>';
            echo '</div></div>';
        }
    }
    
    //Execute the view mode (ie clicking something from the list)
    function viewMode($xml)
    {
        //get the id of the klicked object
        $id = $_GET['bytbilid'];
                
        foreach($xml->children() as $node)
        {
            //when the node matches the id grab the stuff we need
            if ($node->id == $id)
            {
                $miles = $node->miles;
               
                if ($miles == '')
                   $miles = NULL;
                else
                   $miles = $miles.' mil';
                
                //<a href="modules/mod_bytbil/list.php" onclick="popUp('."this.href,'console',800,600".');return false;" target="_blank">
                
                //html motherload
                //320 213 80 53
                echo '<div id="viewwrap">';
                echo '<h2>'.$node->brand .' '. $node->model.'</h2>';
                echo '<div id="viewimagewrap">';
                echo '<div id="viewimageprimer"><a href="'.$node->images->image[0].'" onclick="popUp('."this.href,'console',405,595".');return false;" target="_blank">
                                                <img width="320" height="213" name="sub1" src="'.$node->images->image[0].'" /></a></div>';
                // doing '."'x'".' is not cool
                echo '<div id="viewimagesmaller">';
                                                   
                for ($i=1; $i<=3; $i++)
                {
                    $imgContainer = $node->images->image[$i];
                    //check if there are links with no jpg extensions
                    $isImage = strpos($imgContainer, '.jpg');
                        
                    if (empty($isImage))
                        break;
                    
                    echo'<a href="'.$node->images->image[$i].'" onclick="popUp('."this.href,'console',405,595".');return false;" target="_blank">
                         <img class="outline"
                         onmouseover="roll('."'sub1', '".$node->images->image[$i]."')".'"
                         onmouseout="roll('."'sub1', '".$node->images->image[0]."')".'"
                         width="80" height="53" src="'.$node->images->image[$i].'" /></a>';
                }                                    
                echo '</div></div>';
                echo '<div id="viewtextwrap">';
                echo '<div id="divider"></div>';
                echo '<span id="viewtextbox">Årsmodell:</span><span id="viewnodebox">'. $node->yearmodel.'</span><br />';
                echo '<div id="divider"></div>';
                echo '<span id="viewtextbox">Mätarställning:</span><span id="viewnodebox">'.$miles.'</span><br />';
                echo '<div id="divider"></div>';
                echo '<span id="viewtextbox">Karosseri:</span><span id="viewnodebox">'. $node->bodytype.'</span><br />';
                echo '<div id="divider"></div>';
                echo '<span id="viewtextbox">Pris:</span><span id="viewnodebox">'. $node->{'price-sek'}.' kr</span><br />';
                echo '<div id="divider"></div>';
                echo '<span id="viewtextbox">Färg:</span><span id="viewnodebox">'. $node->color.'</span><br />';
                echo '<div id="divider"></div>';
                echo '<span id="viewtextbox">Växellåda:</span><span id="viewnodebox">'. $node->gearboxtype.'</span><br />';
                echo '<div id="divider"></div>';
                echo '<span id="viewtextbox">Drivmedel:</span><span id="viewnodebox">'. $node->fueltype.'</span><br />';
                echo '<div id="divider"></div>';
                echo '<span id="viewtextbox">Reg. Nummer:</span><span id="viewnodebox">'. $node->regno.'</span><br />';
                echo '<div id="divider"></div>';
                echo '</div>';
                echo '<div id="viewinfobox"><span id="viewtextbox">Info:</span><br />'.$node->info.'</div>';
                echo '<div id="viewinfobox"><span id="viewtextbox">Säljarinfo:</span><br />
                      '.$node->seller.' ('.$node->homepage.') <br />
                      Telefon: '.$node->phone.'<br />E-Post: '.$node->email.'</div>';
                echo '</div>';
            }   
        }
    }
    
    //sets the view for the different subcategories more can be added with little effort
    //$xml refers to the parsable xml-link, $subPage is a reference for subcategories
    function viewSub($xml, $subPage)
    {
        $id = $_GET['bytbilid'];
        
        if ($subPage == 'bilder')
        {
            foreach($xml->children() as $node)
            {
                if ($node->id == $id)
                {
                    echo '<div id="viewwrap">';
                    echo '<div id="viewsubtopimgwrap"><img class="outlinestatic" height="383" name="sub1" src="'.$node->images->image[0].'"/></div>';
                    echo '<div id="viewsubsmallimgwrap">';
                    for ($i = 0; $i <= 9; $i++)
                    {
                        $imgContainer = $node->images->image[$i];
                        //check if there are links with no jpg extensions
                        $isImage = strpos($imgContainer, '.jpg');
                        
                            if (empty($isImage))
                            {
                                break;
                            }
                        
                        echo '<div id="viewsubsmallimg">';
                        echo '<img class="outline" width="80" height="53" onmouseover="roll('."'sub1',"."'".$imgContainer."'".')" src="'.$imgContainer.'"/></div>';
                       
                            if ($i == 4)
                                echo '<br />';
                    }
                    echo '</div>';
                    echo '</div>';
                }
            }
        }
        
        elseif ($subPage == 'interest')
        {
            $category = bytbilHelper::currentPage('sub');
            $staticURL = bytbilHelper::thisURL($category);
            
            foreach($xml->children() as $node)
            {
                if ($node->id == $id)
                {
                    $car = $node->brand .' '. $node->model;
                    $regnr = $node->regno;
                    $seller = $node->seller;
                    $homepage = $node->homepage;
                    $sellerEmail = $node->email;
                    $sellerPhone = $node->phone;
                    $sellerFax = $node->fax;
                    $image = $node->images->image[0];
                }
            }
            echo '<div id="viewwrap">';
            echo 'Tack för att du valt att titta närmare på vår '.$car.'.<br />';
            echo 'Var vänlig fyll i detta enkla formulär så återkommer vi till dig så snart vi kan.<br /><br />';
            echo '<div id="formwrap">';
            echo '<form method="post" action="'.$staticURL.'&send=1"';
            echo '<span id="formtext">Namn: </span><div id="viewformbox"><input id="viewsubtopimg" type="text" name="name"/></div>';
            echo '<div id="dividerblank"></div>';
            echo '<span id="formtext">E-post: </span><div id="viewformbox"><input id="viewsubtopimg" type="text" name="email"/></div>';
            echo '<div id="dividerblank"></div>';
            echo '<span id="formtext">Telefon (Valfritt):  </span><div id="viewformbox"><input id="viewsubtopimg" type="text" name="phone"/></div><br />';
            echo '<div id="dividerblank"></div>';
            echo '<span id="formtext">Meddelande: </span><div id="viewformbox"><textarea id="viewsubtopimg" rows="7" type="text" name="message"/></textarea></div><br />';
            //Joomla security token
            echo JHTML::_( 'form.token' ); 
            echo '<input type="submit" name="sendbut" value="Skicka"/>';
            echo '</form>';
            echo '</div>';
            echo '<div id="formimg"><img class="outlinestatic" width="250" src="'.$image.'"/><br><br>
                  <h3 style="text-align: center">'.$seller.'</h3>Du vet väl att du alltid kan nå oss<br /> dagtid på Telefon: <br /><b>'.$sellerPhone.'</b><br>
                  eller via E-post: <br /><b>'.$sellerEmail.'</b></div>';
            echo '</div>';
        }
        
        elseif ($subPage == 'send')
        {
            foreach($xml->children() as $node)
            {
                if ($node->id == $id)
                {
                    $car = $node->brand .' '. $node->model;
                    $regnr = $node->regno;
                    $seller = $node->seller;
                    $homepage = $node->homepage;
                    $sellerEmail = $node->email;
                }
            }
            
            $phone = NULL; $message = NULL;
            $name = NULL; $email = NULL;
            
            echo '<div id="viewwrap">';
            if (isset ($_POST))
            {
                //check for security token match
                JRequest::checkToken() or die( 'Invalid Form Output (Possible code injection) Operation aborted (press back)' );

                $name = $_POST['name'];
                $email = $_POST['email'];
                $phone = $_POST['phone'];
                $message = $_POST['message'];

                $body = "   <html>
                            <body>
                            $name ($email) Har visat intresse för objekt $car (med regnr: $regnr)<br>
                            <br>
                            Telefon: $phone<br>
                            <br>
                            Meddelande:<br>
                            $message<br>
                            <br>
                            Detta är ett automatiskt genererat epostmeddelande från $seller, $homepage
                            </body>
                           </html>";

                
                if (empty($name) || empty($email))
                    echo 'Epost eller Namn är inte ifyllt. Var vänlig försök igen';
                
                else
                {
                    $send = bytbilHelper::SendMail($sellerEmail, "Intresse anmält på $car regnr: $regnr", $body, $email);
                    if ($send)
                    {
                        echo "Ditt meddelande är skickat. Tack för visat intresse.<br> Vi återkommer till dig så snart vi kan";
                    }
                }
            }
            echo '</div>';
        }
    }
    
    function SendMail($to, $subject, $message, $from)
    {   
	define("ENCODING", "UTF-8");
	$b64subject = "=?UTF-8?B?" . base64_encode($subject) . "?="; //formats subject to allow special characters

	//Unicode headers
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
	// Additional headers
	$headers .= 'From: SoftPower <$from>' . "\r\n";

	if (mail($to, $b64subject, $message, $headers))
	    return true;
	else
	    return false;
    }
    
    //get the url and trim away our own get values. Essensial for back buttons and new links
    function thisURL($page)
    {
               
        if (isset($_GET[$page]))
        {
            $string = '&'.$page.'='.$_GET[$page];
            $length = strlen($string);
            $original = $_SERVER["REQUEST_URI"];
            $string = substr_replace($original, '', -$length);
            return $string;
        }
        else
            return $_SERVER["REQUEST_URI"];
    }
    
    //figures out what page the user is on
    //$scope specifies which scope to look for, main pages or sub pages
    function currentPage($scope)
    {
        $pages = array ('bytbilid'  => 1,
                        'bytbil'    => 1);
        
        $subpages = array ('info' => 1,
                           'bilder' => 1,
                           'interest' => 1,
                           'send' => 1);
        if ($scope == 'main')
        {
            foreach($pages as $index => $content)
            {
                if (isset($_GET[$index]))
                {
                    return $index;
                }           
            }
        }
        
        elseif ($scope == 'sub')
        {
            foreach($subpages as $subindex => $subcontent)
            {
                if (isset($_GET[$subindex]))
                    return $subindex;
            }
        }
    }
    
    //executes the tab menu and applies correct css to tabs
    function menu($page)
    {
        //set links
        $category = bytbilHelper::currentPage('sub');
        $staticURL = bytbilHelper::thisURL($category);
        $infoLink = $staticURL;
        $bilderLink = $staticURL.'&bilder=1';
        $interestLink = $staticURL.'&interest=1';
        
        switch ($category)
        {
            case '':
                $infoId = 'topfocus';
                $bildId = 'topblurright';
                $interestId = 'topblurright';
                break;
            
            case 'bilder':
                $infoId = 'topblurleft';
                $bildId = 'topfocus';
                $interestId = 'topblurright';
                break;
            
            case 'interest' || 'send':
                $infoId = 'topblurleft';
                $bildId = 'topblurleft';
                $interestId = 'topfocus';
                break;
            
            }
        
        echo '<div id="'.$infoId.'"><a id="menulink" href="'.$infoLink.'">Info</a></div><div id="'.$bildId.'"><a id="menulink" href="'.$bilderLink.'">Bilder</a></div><div id="'.$interestId.'"><a id="menulink" href="'.$interestLink.'">Intresseanmälan</a></div><br />';
    }
}    
?>
