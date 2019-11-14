<?php
/***********************************************************
 Copyright (C) 2019 Orange 

 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License
 version 2 as published by the Free Software Foundation.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License along
 with this program; if not, write to the Free Software Foundation, Inc.,
 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 ***********************************************************/

define("TITLE_DASHBOARD", _("Statistics dashboard"));

use Fossology\Lib\Db\DbManager;

class dashboardReporting extends FO_Plugin
{
  protected $pgVersion;

  /** @var DbManager */
  private $dbManager;

  function __construct()
  {
    $this->Name       = "dashboard-statistics";
    $this->Title      = TITLE_DASHBOARD;
    $this->MenuList   = "Admin::Dashboards::Statistics";
    $this->DBaccess   = PLUGIN_DB_ADMIN;
    parent::__construct();
    $this->dbManager = $GLOBALS['container']->get('db.manager');
  }

  // function GetUploadPerType(){
  //   // TODO: I do not get how the upload mode is set. There should be two kinds of result of bitwise operation, but somehow there is more..
  //   // it seems like 100 and 104 are finihed. other lower ids are not fully uploaded?
  //   $query = "select upload_mode,t.mode , count (upload_origin) from upload u,(VALUES('git',100),('file',104)) as t (mode,mode_id) where t.mode_id=u.upload_mode group by u.upload_mode,t.mode;";



  // }

  function GetOtherMeasures(){

    //TODO: consult this query - shloundn't it be exploded to separate queries ??
    $q1 = "SELECT count(u.*) AS users FROM users u";
    $q2 = "SELECT count(g.*) AS groups FROM groups g";
    $q3 = "SELECT count(up.*) as uploads from (select distinct upload_mode,upload_origin from upload) up";
    //it should maybe have a separate function as `GetUploadPerType` (currently commented out above..)
    $q4 = "SELECT count(up1.upload_origin) as file_uploads FROM upload up1 WHERE up1.upload_mode=104";
    $q5 = "SELECT count(up2.upload_origin) as url_uploads FROM upload up2 WHERE up2.upload_mode=100";
    $query = "SELECT * FROM (".$q1.") as q1, (".$q2.") as q2, (".$q3.") as q3, (".$q4.") as q4, (".$q5.") as q5;";
    
    
    $result = $this->dbManager->getSingleRow($query);
    
    $V = "<table border=1 width=350>";
    $V.= "<tr><th>Measure</th><th>Value</th></tr>";
    
    $V.= "<tr><td align=left>Count Users</td><td align=right>".$result['users']."</td></tr>";
    $V.= "<tr><td align=left>Count Groups</td><td align=right>".$result['groups']."</td></tr>";
    $V.= "<tr><td align=left>Distinct uploads (mode+origin)</td><td align=right>".$result['uploads']."</td></tr>";
    $V.= "<tr><td align=left>All File uploads</td><td align=right>".$result['file_uploads']."</td></tr>";
    $V.= "<tr><td align=left>All URL uploads</td><td align=right>".$result['url_uploads']."</td></tr>";
    $V.= "</table>";

    return $V;
  }

  /**
   * \brief Lists number of ever quequed jobs per job type (agent)..
   */
  function CountAllJobs()
  {
    $query = "SELECT ag.agent_name,ag.agent_desc,count(jq.*) AS fired_jobs ";
    $query.= "FROM agent ag LEFT OUTER JOIN jobqueue jq ON (jq.jq_type = ag.agent_name) ";
    $query.= "GROUP BY ag.agent_name,ag.agent_desc ORDER BY fired_jobs DESC;";

    $rows = $this->dbManager->getRows($query);

    $V = "<table border=1>";
    $V .= "<tr><th>".("AgentName")."</th><th>"._("Description")."</th><th>"._("Number of jobs")."</th></tr>";

    foreach ($rows as $agData) {
      $V .= "<tr><td>".$agData['agent_name']."</td><td>".$agData['agent_desc']."</td><td aligalign='right'>".$agData['fired_jobs']."</td></tr>";
    }

    $V .= "</table>";

    return $V;
  }

  public function Output()
  {
      $V = "<h1> Statistics </h1>";
      $V .= "<table style='width: 100%;' border=0>\n";

      $V .= "<tr>";
      $V .= "<td class='dashboard'>";
      $text = _("Jobs Sumary");
      $V .= "<h2>$text</h2>\n";
      $V .= $this->CountAllJobs();
      $V .= "</td>";
      $V .= "<td class='dashboard'>";
      $text = _("Other Measures");
      $V .= "<h2>$text</h2>\n";
      $V .= $this->GetOtherMeasures();
      $V .= "</td>";
      $V .= "</tr>";

      $V .= "</table>";

      return $V;
  }
}

$dash = new dashboardReporting ;
$dash->Initialize();
