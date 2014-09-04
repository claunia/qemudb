<?php
/***************************************************/
/* this class represents a category + its children */
/***************************************************/

/**
 * Category class for handling categories.
 */
class qemuSystem {
    var $iSysId;
    var $iParentId;
    var $sName;
    var $sDescription;
    var $aApplicationsIds;  // an array that contains the appId of every application linked to this category
    var $aSubcatsIds;        // an array that contains the appId of every application linked to this category


    /**    
     * constructor, fetches the data.
     */
    function qemuSystem($iSysId = null)
    {
        // we are working on an existing category
        if($iSysId=="0" || $iSysId)
        {
            /*
             * We fetch the data related to this vendor.
             */
            $sQuery = "SELECT *
                       FROM qemuSystems
                       WHERE qsysId = '?' ORDER BY qsysName;";
            if($hResult = query_parameters($sQuery, $iSysId))
            {
                $oRow = query_fetch_object($hResult);
                if($oRow)
                {
                    $this->iSysId = $iSysId;
                    $this->iParentId = $oRow->qsysParent;
                    $this->sName = $oRow->qsysName;
                    $this->sDescription = $oRow->qsysDescription;
                }
            }

            /*
             * We fetch applicationsIds. 
             */
            $sQuery = "SELECT qsysId
                       FROM appFamily
                       WHERE qsysId = '?'
                       AND state = 'accepted' ORDER BY appName";
            if($hResult = query_parameters($sQuery, $iSysId))
            {
                while($oRow = query_fetch_object($hResult))
                {
                    $this->aApplicationsIds[] = $oRow->appId;
                }
            }

            /*
             * We fetch subqsysIds. 
             */
            $sQuery = "SELECT qsysId
                       FROM qemuSystems
                       WHERE qsysParent = '?' ORDER BY qsysName;";
            if($hResult = query_parameters($sQuery, $iSysId))
            {
                while($oRow = query_fetch_object($hResult))
                {
                    $this->aSubcatsIds[] = $oRow->qsysId;
                }
            }
        }
    }


    /**
     * Creates a new category.
     */
    function create()
    {
        $hResult = query_parameters("INSERT INTO qemuSystems (qsysName, qsysDescription, qsysParent) ".
                                    "VALUES('?', '?', '?')",
                                    $this->sName, $this->sDescription, $this->iParentId);
        if($hResult)
        {
            $this->iSysId = query_appdb_insert_id();
            $this->qemuSystem($this->iSysId);
            return true;
        }

        return false;
    }

    /**
     * Update category.
     * Returns true on success and false on failure.
     */
    function update()
    {
        if(!query_parameters("UPDATE qemuSystems SET qsysName = '?', qsysDescription = '?', qsysParent = '?' WHERE qsysId = '?'",
                             $this->sName, $this->sDescription, $this->iParentId, $this->iSysId))
            return false;

        return true;
    }


    /**    
     * Deletes the category from the database. 
     */
    function delete()
    {
        if(!$this->canEdit())
            return false;

        if(sizeof($this->aApplicationsIds)>0)
            return FALSE;

        $sQuery = "DELETE FROM qemuSystems 
                    WHERE qsysId = '?' 
                    LIMIT 1";
        query_parameters($sQuery, $this->iSysId);

        return true;
    }

    function objectGetMailOptions($sAction, $bMailSubmitter, $bParentAction)
    {
        return new mailOptions();
    }

    function objectGetChildren()
    {
        /* We don't have any (or we do, sort of, but we don't use them for anything at the moment) */
                return array();
    }

    /* Get a category's subcategory objects.  Names are indented according
       to subcategory level */
    function getSubCatList($iLevel = 0)
    {
        $aOut = array();
        $iId = $this->iSysId ? $this->iSysId : 0;

        $sIndent = '';
        for($i = 0; $i < $iLevel; $i++)
            $sIndent .= '&nbsp; &nbsp;';

        $hResult = query_parameters("SELECT * FROM qemuSystems WHERE qsysParent = '?'
                                     ORDER BY qsysName", $iId);

        while($oRow = mysql_fetch_object($hResult))
        {
            $oCat = new qemuSystem($oRow->qsysId);
            $oCat->sName = $sIndent.$oCat->sName;
            $aOut[] = $oCat;
            $aOut = array_merge($aOut, $oCat->getSubCatList($iLevel + 1));
        }
        return $aOut;
    }

    /* Get all category objects, ordered and with category names indented
       according to subcategory level.
       Optionally includes the 'Main' top category. */
    static function getOrderedList($bIncludeMain = false)
    {
        $oCat = new qemuSystem();

        if(!$bIncludeMain)
            return $oCat->getSubCatList(0);

        $oCat->sName = 'Main';
        $aCats = array($oCat);
        $aCats = array_merge($aCats, $oCat->getSubCatList(1));

        return $aCats;
    }

    /* Returns an SQL statement that will match items in the current category
       and all sub-categories */
    public function getSqlQueryPart()
    {
        $sRet = '';
        $aSubCats = $this->getSubCatList();
        $sRet .= " ( qsysId = '{$this->iSysId}' ";
        foreach($aSubCats as $oCat)
        {
            $iSysId = $oCat->objectGetId();
            $sRet .= " OR qsysId = '$iSysId' ";
        }
        $sRet .= ") ";

        return $sRet;
    }

    function objectGetMail($sAction, $bMailSubmitter, $bParentAction)
    {
        /* We don't send notification mails */
                return array(null, null, null);
    }

    /**
     * returns a path like:
     *
     *     { ROOT, Games, Simulation }
     */
    function getCategoryPath()
    {
        $aPath = array();
        $iSysId  = $this->iSysId;

        /* loop, working up through categories until we have no parent */
        while($iSysId != 0)
        {
            $hResult = query_parameters("SELECT qsysName, qsysId, qsysParent FROM qemuSystems WHERE qsysId = '?'",
                                       $iSysId);
            if(!$hResult || query_num_rows($hResult) != 1)
                break;
            $oCatRow = query_fetch_object($hResult);
            $aPath[] = array($oCatRow->qsysId, $oCatRow->qsysName);
            $iSysId = $oCatRow->qsysParent;
        }
        $aPath[] = array(0, "ROOT");
        return array_reverse($aPath);
    }

    /* return the total number of applications in this category */
    function getApplicationCount($depth = null)
    {
        $MAX_DEPTH = 5;

        if($depth)
            $depth++;
        else
            $depth = 0;

        /* if we've reached our max depth, just return 0 and stop recursing */
        if($depth >= $MAX_DEPTH)
            return 0;

        $totalApps = 0;

        /* add on all apps in each category this category includes */
        if($this->aSubcatsIds)
        {
            while(list($i, $iSubqsysId) = each($this->aSubcatsIds))
            {
                $subCat = new qemuSystem($iSubqsysId);
                $totalApps += $subCat->getApplicationCount($depth);
            }
        }

        $totalApps += sizeof($this->aApplicationsIds); /* add on the apps at this category level */
        
        return $totalApps;
    }

    /**
     * create the Category: line at the top of appdb pages$
     */
    function make_cat_path($aPath, $iAppId = '', $iVersionId = '')
    {
        $sStr = "";
        $iCatCount = 0;
        while(list($iSysIdx, list($iSysId, $sName)) = each($aPath))
        {
            if($sName == "ROOT")
                $sqsysName = "Main";
            else
                $sqsysName = $sName;

            if ($iCatCount > 0) $sStr .= " &gt; ";
            $sStr .= html_ahref($sqsysName,"objectManager.php?sClass=qemuSystem&iId=$iSysId&sAction=view&sTitle=Browse+OSes");
            $iCatCount++;
        }

        if($iAppId)
        {
            $oApp = new Application($iAppId);
            if($iVersionId)
            {
                $oVersion = new Version($iVersionId);
                $sStr .= " &gt; ".$oApp->objectMakeLink();
                $sStr .= " &gt; ".$oVersion->sName;
            } else
            {
                $sStr .= " &gt; ".$oApp->sName;
            }
        }

        return $sStr;
    }

    public function objectGetState()
    {
        // We currenly don't queue categories
        return 'accepted';
    }

    function objectGetId()
    {
        return $this->iSysId;
    }

    public function objectMakeLink()
    {
        return '<a href="'.$this->objectMakeUrl()."\">{$this->sName}</a>'";
    }

    public function objectMakeUrl()
    {
        return BASE."objectManager.php?sClass=qemuSystem&sAction=view&iId={$this->iSysId}&sTitle=Browse+OSes";
    }

    public function objectAllowNullId($sAction)
    {
        switch($sAction)
        {
            case 'view':
                return true;

            default:
                return false;
        }
    }

    function objectGetSubmitterId()
    {
        /* We don't log that */
        return 0;
    }

    function objectGetCustomVars($sAction)
    {
        switch($sAction)
        {
            case 'add':
                return array('iParentId');

            default:
                return null;
        }
    }

    function outputEditor($aValues = null)
    {
        $aCategories = qemuSystem::getOrderedList(true);
        $aqsysNames = array();
        $aCatIds = array();

        $iParentId = $this->iParentId;
        if(!$iParentId && $aValues)
            $iParentId = getInput('iParentId', $aValues);

        foreach($aCategories as $oCategory)
        {
            $aqsysNames[] = $oCategory->sName;
            $aCatIds[] = $oCategory->objectGetId();
        }

        echo "<table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"2\">
                <tr>
                <td width=\"15%\" class=\"box-label\"><b>System name</b></td>
                <td class=\"box-body\">
                <input type=\"text\" size=\"50\" name=\"sName\" value=\"".$this->sName."\"> 
                </td>
                </tr>
                <tr>
                <td width=\"15%\" class=\"box-label\"><b>Description</b></td>
                <td class=\"box-body\">
                <input type=\"text\" size=\"50\" name=\"sDescription\" value=\"".$this->sDescription."\"> 
                </td>
                </tr>
                <tr>
                <td width=\"15%\" class=\"box-label\"><b>Parent</b></td>
                <td class=\"box-body\">
                ".html_select("iParentId",$aCatIds,$iParentId, $aqsysNames)." 
                </td>
                </tr>
                </table>";
    }

    function allowAnonymousSubmissions()
    {
        return FALSE;
    }

    function getOutputEditorValues($aClean)
    {
        $this->sName = $aClean['sName'];
        $this->iParentId = $aClean['iParentId'];
        $this->sDescription = $aClean['sDescription'];
    }

    function mustBeQueued()
    {
        return $_SESSION['current']->hasPriv('admin');
    }

    function canEdit()
    {
        return $_SESSION['current']->hasPriv('admin');
    }

    /**
     * display the full path of the Category we are looking at
     */
    function displayPath($appId, $versionId = '')
    {
        $sCatFullPath = qemuSystem::make_cat_path($this->getCategoryPath(), $appId, $versionId);
        echo html_frame_start("",'98%','',2);
        echo "<p><b>System: ". $sCatFullPath ."</b><br>\n";
        echo html_frame_end();
    }

    public function display()
    {
        // list sub categories
        $sCatFullPath = qemuSystem::make_cat_path($this->getCategoryPath());
        $aSubs = $this->aSubcatsIds;

        echo "<div class='default_container'>\n";

        // Allow editing categories
        if($this->canEdit())
        {
            $oM = new objectManager('qemuSystem', '', $this->iSysId);
            $oM->setReturnTo($this->objectMakeUrl());
            echo "<p>\n";
            echo '<a href="'.$oM->makeUrl('add', null, 'Add system')."&iParentId={$this->iSysId}\">Add</a>";;
            if($this->iSysId) // We can't edit the 'Main' category
            {
                echo ' &nbsp; &nbsp; ';
                echo '<a href="'.$oM->makeUrl('edit', $this->iSysId, 'Edit system').'">Edit</a>';
                echo ' &nbsp; &nbsp; ';
                echo '<a href="'.$oM->makeUrl('delete', $this->iSysId, 'Delete system').'">Delete</a>';
            }
            echo "</p>\n";
        }

        // Output sub-categories
        if($aSubs)
        {
            echo html_frame_start("",'98%','',2);
            echo "<p><b>System: ". $sCatFullPath ."</b><br>\n";
            echo html_frame_end();

            echo html_frame_start("","98%","",0);

            $oTable = new Table();
            $oTable->SetWidth("100%");
            $oTable->SetBorder(0);
            $oTable->SetCellPadding(3);
            $oTable->SetCellSpacing(1);

            $oTableRow = new TableRow();
            $oTableRow->SetClass("color4");
            $oTableRow->AddTextCell("Sub system");
            $oTableRow->AddTextCell("Description");
            $oTableRow->AddTextCell("No. OSes");
            $oTable->SetHeader($oTableRow);
            
            while(list($i,$iSubqsysId) = each($aSubs))
            {
                $oSubCat= new qemuSystem($iSubqsysId);

                //set row color
                $sColor = ($i % 2) ? "color0" : "color1"; 

                $oTableRowHighlight = GetStandardRowHighlight($i);

                $sUrl = $oSubCat->objectMakeUrl();

                $oTableRowClick = new TableRowClick($sUrl);
                $oTableRowClick->SetHighlight($oTableRowHighlight);

                //get number of apps in this sub-category
                $iAppcount = $oSubCat->getApplicationCount();

                //format desc
                $sDesc = substr(stripslashes($oSubCat->sDescription),0,70);

                //display row
                $oTableRow = new TableRow();
                $oTableRow->SetClass($sColor);
                $oTableRow->SetRowClick($oTableRowClick);

                $oTableCell = new TableCell($oSubCat->sName);
                $oTableCell->SetCellLink($sUrl);
                $oTableRow->AddCell($oTableCell);
                $oTableRow->AddTextCell("$sDesc &nbsp;");
                $oTableRow->AddTextCell("$iAppcount &nbsp;");

                $oTable->AddRow($oTableRow);
            }

            // output the table
            echo $oTable->GetString();

            echo html_frame_end( count($aSubs) . ' categories');
        }


        // list applications in this category
        $aApps = $this->aApplicationsIds;
        if($aApps)
        {
            echo html_frame_start("",'98%','',2);
            echo "<p><b>System: ". $sCatFullPath ."</b><br>\n";
            echo html_frame_end();
            
            echo html_frame_start("","98%","",0);

            $oTable = new Table();
            $oTable->SetWidth("100%");
            $oTable->SetBorder(0);
            $oTable->SetCellPadding(3);
            $oTable->SetCellSpacing(1);

            $oTableRow = new TableRow();
            $oTableRow->SetClass("color4");
            $oTableRow->AddTextCell("Operating system name");
            $oTableRow->AddTextCell("Description");
            $oTableRow->AddTextCell("No. Versions");

            $oTable->SetHeader($oTableRow);
                    
            while(list($i, $iAppId) = each($aApps))
            {
                $oApp = new Application($iAppId);

                //set row color
                $sColor = ($i % 2) ? "color0" : "color1";

                $oTableRowHighlight = GetStandardRowHighlight($i);

                $sUrl = $oApp->objectMakeUrl();

                $oTableRowClick = new TableRowClick($sUrl);
                $oTableRowClick->SetHighlight($oTableRowHighlight);
                
                //format desc
                $sDesc = util_trim_description($oApp->sDescription);
                
                //display row
                $oTableRow = new TableRow();
                $oTableRow->SetRowClick($oTableRowClick);
                $oTableRow->SetClass($sColor);
                $oTableRow->AddTextCell($oApp->objectMakeLink());
                $oTableRow->AddTextCell("$sDesc &nbsp;");
                $oTableRow->AddTextCell(sizeof($oApp->aVersionsIds));

                $oTable->AddRow($oTableRow);
            }
            
            // output table
            echo $oTable->GetString();

            echo html_frame_end( count($aApps) . " operating systems in this system");
        }

        // Show a message if this category is empty
        if(!$aApps && !$aSubs)
        {
            echo html_frame_start("",'98%','',2);
            echo "<p><b>System: ". $sCatFullPath ."</b><br>\n";
            echo html_frame_end();

            echo html_frame_start('','90%','',2);
            echo 'This system has no sub-systems or operating systems';
            echo html_frame_end();
        }
    }
}

?>
