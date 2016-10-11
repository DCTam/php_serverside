<!DOCTYPE html>
<?php
    define("URL_TO_TRACKER_SITE", "http://www.google.com/");
?>

<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Hw2</title>
        <link rel="stylesheet" type="text/css" href="analytics.css">
    </head>

    <body>


        <?php if(isset($_REQUEST['activity'])):

            switch($_REQUEST['activity']){
                case 'codes': codes(); break;

                case 'analytics': analytics(); break;
                    
                case 'counts': counts(); break;

                default: landing(); break;

            }
        else: landing();
        endif; ?>

    </body>
</html>

<?php function landing(){ ?>
    <h1>Web Page Tagging Analytics</h1>
    <form name="argForm" method="get">
        <input type="text" name="arg" value="<?php echo isset($_REQUEST['arg']) ? $_REQUEST['arg'] : '';?>" placeholder="Enter Site Magic String"/>
        <select name="activity">
            <option value="analytics">View Analytics</option>
            <option value="codes">Get Site Tracker Codes</option>
        </select>
        <button type='submit'>Go</button>
    </form>
<?php } ?>

<?php function codes(){
    if($_REQUEST['arg'] != ""):
        $tempArg = $_REQUEST['arg'];
        if(!isset($_REQUEST['arg2'])): ?>
            <h1>Tracker Codes - Web Page Tagging Analytics</h1>
            <form name="argForm2" method="get">
                <input type="text" name="arg2" value="<?php echo isset($_REQUEST['arg2']) ? $_REQUEST['arg2'] : '';?>" placeholder="Enter a URL to track"/>
                <input type="hidden" name="activity" value="codes"/>
                <input type="hidden" name="arg" value="<?php echo "$tempArg"; ?>"/>
                <button type="submit">Go</button>
            </form>
        <?php
        else:
            $XXXX = sha1($_REQUEST['arg'] . $_REQUEST['arg2']);
            $arg2 = $_REQUEST['arg2'];
            $YYYY = sha1($_REQUEST['arg']);
        ?>
            <h1>Tracker Codes - Web Page Tagging Analytics</h1>
            <form name="argForm3" method="get">
                <input type="text" name="arg2" value="<?php echo "$arg2"; ?>" placeholder="Enter a URL to track"/>
                <input type="hidden" name="activity" value="codes"/>
                <input type="hidden" name="arg" value="<?php echo "$YYYY"; ?>"/>
                <button type="submit">Go</button>
            </form>

            <h2>Add the following code to the web page of the site with the url just entered</h2>

        <?php
            $tempScript = "<script src=\"" . constant("URL_TO_TRACKER_SITE") . "?activity=counts&arg=" . $YYYY . "&arg2=" . $XXXX . "\"/>";
            echo htmlentities($tempScript);
        endif;
        if(file_exists("url_lookups.txt")):
            $fileHandle = fopen("url_lookups.txt", "a+");
            $fileContent = fread($fileHandle, filesize("url_lookups.txt"));
            $lookups = unserialize($fileContent);
            $lookups[$XXXX] = $_REQUEST['arg2'];
            $serializedLookups = serialize($lookups);
            file_put_contents("url_lookups.txt", "");
            fwrite($fileHandle, $serializedLookups);   
            fclose($fileHandle);

        else:
            $lookups = [];
            $lookups[$XXXX] = $_REQUEST['arg2'];
            $serializedContent = serialize($lookups);
            $fileHandle = fopen("url_lookups.txt", "w");
            fwrite($fileHandle, $serializedContent);
            fclose($fileHandle);
    
        endif;

    else: landing();
    endif;
} ?>

<?php function counts(){ ?>
    <?php
    //Testing web address to force load counts()
    //http://localhost/HW2/analytics/index.php?arg2=www.yahoo.com&activity=counts&arg=yahoo
    $IP = $_SERVER['REMOTE_ADDR'];
      if((isset($_REQUEST['arg']) && $_REQUEST['arg'] != "") && (isset($_REQUEST['arg2']) && $_REQUEST['arg2'] != "")):
        if(file_exists('counts.txt')):
          $fileHandle = fopen("counts.txt", "r+");
          $fileContent = fread($fileHandle, filesize("counts.txt"));
          $counts = unserialize($fileContent);
          fclose($fileHandle);
          if(!(isset($counts[$_REQUEST['arg']][$_REQUEST['arg2']][$IP]))):
            $counts[$_REQUEST['arg']][$_REQUEST['arg2']][$IP] = '1';
          else:
          $counts[$_REQUEST['arg']][$_REQUEST['arg2']][$IP]++;
        endif;
        else:
          $counts = [];
          if(!(isset($counts[$_REQUEST['arg']][$_REQUEST['arg2']][$IP]))):
            $counts[$_REQUEST['arg']][$_REQUEST['arg2']][$IP] = '1';
          endif;
          endif;
        endif;
          $serialCount = serialize($counts);
          $fileHandle = fopen("counts.txt", "w") or die("Unable to open file!");
          fwrite($fileHandle, $serialCount);
          fclose($fileHandle);
          echo "tracking = \"done\"";
        return;
<?php } ?>

<?php function analytics(){ ?>
    <h1>View Analytics - Web Page Tagging Analytics</h1>
    <h2>Analytics for <?php echo $_REQUEST['arg']; ?></h2>

    <?php
        //Open I/O for both url_lookups.txt and counts.txt
        $fileHandleURL = fopen("url_lookups.txt", "r+");
        $fileContentURL = fread($fileHandleURL, filesize("url_lookups.txt"));
        $fileHandleCounts = fopen("counts.txt", "r+");
        $fileContentCounts = fread($fileHandleCounts, filesize("counts.txt"));        
        
        //Unserialize both txt files               
        $tempURL = (unserialize($fileContentURL));
        $tempCounts = (unserialize($fileContentCounts));
                           
        //Loop through url_lookups.txt YYYY value with counts.txt XXXX value and display the URl and count
        foreach($tempURL as $ele){ 
            if($tempCounts[$_REQUEST['arg']][$ele][$_SERVER['REMOTE_ADDR']]): 
                $holdEle = $ele;
                $holdCount = $tempCounts[$_REQUEST['arg']][$ele][$_SERVER['REMOTE_ADDR']];
                ?>

                <h3>URL: <?php echo $holdEle ?> Total Count: <?php echo $holdCount ?></h3>

                <table>
                    <tr>
                        <th>IP Address</th>
                        <th>Hits from this IP</th>
                    </tr>
                    <tr>
                        <td><?php echo $_SERVER['REMOTE_ADDR']?></td>
                        <td><?php echo $holdCount ?></td>
                    </tr>
                </table>
                
        <?php 
                    
            endif;
        }                           

    ?>

<?php } ?>
