<?php
/************************************************/
/* Show all applications that have a bug link # */
/************************************************/

include("path.php");
require(BASE."include/incl.php");

/* code to View versions affected by a Bug */
$bug_id = $_REQUEST['bug_id'];

if(!is_numeric($bug_id))
{
    errorpage("Something went wrong with the bug ID");
    exit;
}
{
    apidb_header("Applications affected by Bug #".$bug_id);
    echo '<form method=post action="viewbugs.php?bug_id='.$bug_id.'">',"\n";

    echo '<table width=100% border=0 cellpadding=3 cellspacing=1>',"\n";
    echo '<tr class=color4>',"\n";
    echo '    <td width=80>Application Name</td>',"\n";
    echo '    <td>Description</td>',"\n";
    echo '    <td width=80>version</td>',"\n";
    echo '</tr>',"\n";


    $sQuery = "SELECT appFamily.description as appDescription,
               appFamily.appName as appName,
               appVersion.*, buglinks.versionId as versionId
               FROM appFamily, appVersion, buglinks
               WHERE appFamily.appId = appVersion.appId 
               and buglinks.versionId = appVersion.versionId
               AND buglinks.bug_id = ".$bug_id."
               ORDER BY versionName";
    $c = 0;

    if($hResult = query_appdb($sQuery))
    {
        while($oRow = mysql_fetch_object($hResult))
        {
            // set row color
            $bgcolor = ($c % 2 == 0) ? "color0" : "color1";
            echo '<tr class='.$bgcolor.'>',"\n";
            echo '    <td>',"\n";
            echo '    <a href="appview.php?appId='.$oRow->appId.'">'.$oRow->appName.'</a>',"\n";
            echo '    </td>',"\n";
            echo '    <td>'.$oRow->appDescription.'</td>',"\n";
            echo '    <td>',"\n";
            echo '    <a href="appview.php?versionId='.$oRow->versionId.'">'.$oRow->versionName.'</a>',"\n";
            echo '    </td>',"\n";
            echo '</tr>',"\n";
        }
    }

    /* allow users to search for other apps */

    echo '<tr class=color2>',"\n";
    echo '    <td align=center colspan=5>&nbsp</td>',"\n";
    echo '</tr>',"\n";

    echo '<tr class=color4>',"\n";
    echo '    <td colspan=3 >&nbsp Bug #</td>',"\n";
    echo '</tr>',"\n";

    echo '<tr class=color3>',"\n";
    echo '    <td align=center>',"\n";
    echo '    <input type="text" name="bug_id" value="'.$bug_id.'" size="8"></td>',"\n";
    echo '    <td colspan=2><input type="submit" name="sub" value="Search"></td>',"\n";
    echo '</tr>',"\n";

    echo '</table>',"\n";
    apidb_footer();

}
?>