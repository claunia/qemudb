<?php
/**
 * Application database index page.
 *
 * TODO:
 *  - rename outputTopXRowAppsFromRating according to our coding standards
 *  - rename variables that don't follow our coding standards
 */

// application environment
require("path.php");
require(BASE."include/incl.php");

apidb_header("QEMU Official OS Support List");
?>
    <!-- <img style="float:right;" src="images/appdb_montage.jpg" width=391 height=266 alt="Wine AppDB"> --!>

<div class='default_container'>    
<h1>Welcome</h1>

<p>This is the QEMU Official OS Support List. Here you can get information on operating system
compatibility with QEMU.</p>
<?php
$str_benefits="
    <ul>
        <li>Ability to <a href=\"".BASE."help/?sTopic=voting\" title=\"help on voting\" style=\"cursor: help\">vote</a> on your favorite operating systems</li>
        <li>Ability to customize the layout and behavior of the OS List and comments system</li>
        <li>Take credit for your witty posts</li>
        <li>Submit new operating systems and versions</li>
        <li>Submit new screenshots</li>
    </ul>
";
if(!$_SESSION['current']->isLoggedIn()) 
{
    echo "
    <p>Most of the features of the Official OS Support List require that you have a user account and
    are logged in. Some of the benefits of membership are:<p>

    $str_benefits

    <p>So, what are you waiting for? [<a href=\"".login_url()."\">Log in</a>]
    or [<a href=\"account.php?sCmd=new\">register</a>] now! Your help in
    stomping out QEMU issues will be greatly appreciated.</p>";
} else 
{
    echo "
    <p>As an Official OS Support List member you enjoy some exclusive benefits like:<p>

    $str_benefits

    <p>We&#8217;d like to thank you for being a member and being logged in to the system. Your help in
    stomping out QEMU issues will be greatly appreciated.</p>";

}

?>
<?php

    $iNumApps = version::objectGetEntriesCount('accepted');

    $voteQuery = "SELECT appVotes.versionId, count(userId) as count ".
        "FROM appVotes ".
        "GROUP BY versionId ORDER BY count DESC LIMIT 1";
    $hResult = query_parameters($voteQuery);
    $oRow = query_fetch_object($hResult);

    echo "There are <b>$iNumApps</b> operating systems currently in the database,";

    // don't mention the top application if there are no votes yet
    if( !empty($oRow) )
    {
        if($oRow->versionId)
        {
            $shVoteAppLink = version::fullNameLink($oRow->versionId);
            echo " with $shVoteAppLink being the\n";
            echo "top <a href='votestats.php'>voted</a> operating system.\n";
        } else
        {
            echo " please <a href=\"".BASE."help/?sTopic=voting\" title=\"help on voting\"".
                "style=\"cursor: help\">vote</a> for your favourite operating system.\n";
        }
    }
?>

<br><br>

<div class="topx_style platinum">
  <div class="rating_header">
    <div class="rating_title">
      Top-10 <a href="objectManager.php?sClass=application&sTitle=Browse+Applications&iappVersion-ratingOp0=5&sappVersion-ratingData0=Platinum&sOrderBy=appName&bAscending=true">Platinum</a> List
    </div>
    Operating systems which install and run flawlessly on QEMU with any hardware combination tested.
  </div>
  <div>
    <table class="platinum">
      <tr class="rowtitle">
        <th>Operating System</th><th>Description</th><th>Screenshot</th>
      </tr>
      <?php
      outputTopXRowAppsFromRating('Platinum', 10);
      ?>
    </table>
  </div>
</div>
<br>

<div class="topx_style gold">
  <div class="rating_header">
    <div class="rating_title">
      Top-10 <a href="objectManager.php?sClass=application&sTitle=Browse+Applications&iappVersion-ratingOp0=5&sappVersion-ratingData0=Gold&sOrderBy=appName&bAscending=true">Gold</a> List
    </div>
    Operating systems that work flawlessly with some special configuration
  </div>
  <div>
    <table class="gold">
      <tr class="rowtitle">
        <th>Operating System</th><th>Description</th><th>Screenshot</th>
      </tr>
      <?php
      outputTopXRowAppsFromRating('Gold', 10);
      ?>
    </table>
  </div>
</div>
<br>

<div class="topx_style silver">
  <div class="rating_header">
    <div class="rating_title">
      Top-10 <a href="objectManager.php?sClass=application&sTitle=Browse+Applications&iappVersion-ratingOp0=5&sappVersion-ratingData0=Silver&sOrderBy=appName&bAscending=true">Silver</a> List
    </div>
    Operating systems with minor issues that do not affect typical usage
  </div>
  <div>
    <table class="silver">
      <tr class="rowtitle">
        <th>Operating System</th><th>Description</th><th>Screenshot</th>
      </tr>
      <?php
      outputTopXRowAppsFromRating('Silver', 10);
      ?>
    </table>
  </div>
</div>

<br><br>

<?php
apidb_footer();
?>
