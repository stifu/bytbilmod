<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<!-- Bytbilmodul utvecklad av SoftPower Technology AB www.softpower.se -->
<?php

    if (isset($page))
    {
        bytbilHelper::menu($page);
        if (!empty($subPage))
            bytbilHelper::viewSub($xml, $subPage);
        else
            bytbilHelper::viewMode($xml);
    }
    
    else
    {
        echo '<h1><a href="'.$defaultURL.'"> Bilar i lager</a></h1>';
        bytbilHelper::listMode($xml, $defaultURL);
    }
    
?>
