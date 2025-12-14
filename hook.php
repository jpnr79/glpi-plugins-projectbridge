/**
 * this function update the progessPercent of processTask when a ticketTask is add or update with time associate.
 * @param TicketTask $ticket_task
 */
function updateProjectTaskProgressPercent(TicketTask $ticket_task) {
    // search if entry exist for the associate ticket
    $ticketId = $ticket_task->fields['tickets_id'];
    $bridge_ticket = new PluginProjectbridgeTicket();
    $results = $bridge_ticket->find(['ticket_id' => $ticketId]);
    foreach ($results as $result) {
        if (is_array($result) && $result['projecttasks_id'] > 0) {
            $projectTask = new ProjectTask();
            $projectTask->getFromDB($result['projecttasks_id']);
            $project_id = $projectTask->fields['projects_id'];
            $pluginProjectbridgeContract = new PluginProjectbridgeContract();
            $pluginProjectbridgeContracts = $pluginProjectbridgeContract->find(['project_id' => $project_id]);
            foreach ($pluginProjectbridgeContracts as $pgc) {
                $contract_id = $pgc['contract_id'];
                PluginProjectbridgeTask::updateProjectTaskProgressPercent($result['projecttasks_id'], $contract_id);
            }
        }
    }
}

/**
 * Hook called after showing a tab
 *
 * @param  array $tab_data
 * @return void
 */
function plugin_projectbridge_post_show_tab(array $tab_data) {
    if (!empty($tab_data['item']) && is_object($tab_data['item']) && !empty($tab_data['options']['itemtype'])) {
        if ($tab_data['options']['itemtype'] == 'Projecttask_Ticket' || $tab_data['options']['itemtype'] == 'ProjectTask_Ticket') {
            if ($tab_data['item'] instanceof Ticket) {
                // add a line to allow linking ticket to a project task
                PluginProjectbridgeTicket::postShow($tab_data['item']);
            } else if ($tab_data['item'] instanceof ProjectTask) {
                // add data to the list of tickets linked to a project task
                PluginProjectbridgeTicket::postShowTask($tab_data['item']);
            }
        } else if ($tab_data['options']['itemtype'] == 'ProjectTask' && $tab_data['item'] instanceof Project) {
            // add a link to the linked contract after showing the list of tasks in a project
            PluginProjectbridgeContract::postShowProject($tab_data['item']);
            // customize the duration columns
            PluginProjectbridgeTask::customizeDurationColumns($tab_data['item']);
        }
    }
}

/**
 * Add new search options
 *
 * @param string $itemtype
 * @return array
 */
function plugin_projectbridge_getAddSearchOptionsNew($itemtype) {
    $options = [];

    switch ($itemtype) {
        case 'Entity':
            $options[] = [
                'id' => 4200,
                'name' => 'ProjectBridge',
            ];

            $options[] = [
                'id' => 4201,
                'table' => PluginProjectbridgeEntity::$table_name,
                // trick GLPI search into thinking we want the contract id so the addSelect function is called
                'field' => 'contract_id',
                'name' => __('Default contract', 'projectbridge'),
                'massiveaction' => false,
            ];

            $options[] = [
                'id' => 4202,
                'table' => PluginProjectbridgeTicket::$table_name,
                'field' => 'project_id',
                'name' => __('Time not affected to a project task (hours)', 'projectbridge'),
                'massiveaction' => false,
            ];
            break;

        case 'Ticket':
            $options[] = [
                'id' => 4210,
                'name' => 'ProjectBridge',
            ];

            //            $options[] = [
            //              'id' => 4211,
            //              'table' => PluginProjectbridgeTicket::$table_name,
            //              'field' => 'project_id',
            //              'name' => 'Projet',
            //              'massiveaction' => false,
            //            ];

            $options[] = [
                'id' => 4212,
                'table' => PluginProjectbridgeTicket::$table_name,
                'field' => 'project_id',
                'name' => __('Project tasks', 'projectbridge'),
                'massiveaction' => false,
                'datatype' => 'text'
            ];

            $options[] = [
                'id' => 4213,
                'table' => PluginProjectbridgeTicket::$table_name,
                'field' => 'project_id',
                'name' => __('ProjectTask status', 'projectbridge'),
                'massiveaction' => false,
            ];

            $options[] = [
                'id' => 4214,
                'table' => PluginProjectbridgeTicket::$table_name,
                'field' => 'project_id',
                'name' => __('Is linked to a project task', 'projectbridge') . ' ?',
                'massiveaction' => false,
                'datatype' => 'bool'
            ];
            $options[] = [
                'id' => 4231,
                'table' => PluginProjectbridgeTicket::$table_name,
                'field' => 'project_id',
                'name' => __('Effective duration (hours)', 'projectbridge'),
                'massiveaction' => false,
                'datatype' => 'decimal',
            ];

            break;

        case 'Contract':
            $options[] = [
                'id' => 4220,
                'name' => 'ProjectBridge',
            ];

            $options[] = [
                'id' => 4221,
                'table' => PluginProjectbridgeContract::$table_name,
                'field' => 'project_id',
                'name' => __('Project name', 'projectbridge'),
                'massiveaction' => false,
            ];

            $options[] = [
                'id' => 4222,
                'table' => PluginProjectbridgeContract::$table_name,
                'field' => 'project_id',
                'name' => __('ProjectBridge project tasks', 'projectbridge'),
                'massiveaction' => false,
            ];
            break;

        case 'projecttask':
            $options[] = [
                'id' => 4230,
                'name' => 'ProjectBridge',
            ];

            $options[] = [
                'id' => 4231,
                'table' => PluginProjectbridgeTicket::$table_name,
                'field' => 'project_id',
                'name' => __('Effective duration (hours)', 'projectbridge'),
                'massiveaction' => false,
            ];

            $options[] = [
                'id' => 4232,
                'table' => PluginProjectbridgeTicket::$table_name,
                'field' => 'project_id',
                'name' => __('Planned duration (hours)', 'projectbridge'),
                'massiveaction' => false,
            ];

            $options[] = [
                'id' => 4233,
                'table' => PluginProjectbridgeTicket::$table_name,
                'field' => 'project_id',
                'name' => __('Last project task ?', 'projectbridge'),
                'massiveaction' => false,
            ];

            $options[] = [
                'id' => 4234,
                'table' => PluginProjectbridgeTicket::$table_name,
                'field' => 'project_id',
                'name' => __('Project status', 'projectbridge'),
                'massiveaction' => false,
            ];
            $options[] = [
                'id' => 4235,
                'table' => PluginProjectbridgeTicket::$table_name,
                'field' => 'project_id',
                'name' => __('Associate tickets', 'projectbridge'),
                'massiveaction' => false,
            ];
            $options[] = [
                'id' => 4236,
                'table' => PluginProjectbridgeTicket::$table_name,
                'field' => 'project_id',
                'name' => __('Comsuption', 'projectbridge'),
                'massiveaction' => false,
            ];

            break;

        case 'Project':
            $options[] = [
                'id' => 4230,
                'name' => 'ProjectBridge',
            ];

            $options[] = [
                'id' => 4231,
                'table' => PluginProjectbridgeContract::$table_name,
                'field' => 'project_id',
                'name' => __('Number of project tasks tickets', 'projectbridge'),
                'massiveaction' => false,
            ];

            break;

        default:
        // nothing to do
    }

    return $options;
}

/**
 * Add a custom select part to search
 *
 * @param string $itemtype
 * @param string $key
 * @param integer $offset
 * @return string
 */
function plugin_projectbridge_addSelect($itemtype, $key, $offset) {
    global $CFG_GLPI;
    $select = "";
    $onlypublicTasks = false;
    if (!Session::haveRight("task", CommonITILTask::SEEPRIVATE) || PluginProjectbridgeConfig::getConfValueByName('CountOnlyPublicTasks')) {
        $onlypublicTasks = true;
    }
    switch ($itemtype) {
        case 'Entity':
            if ($key == 4201) {
                $contract_link = rtrim($CFG_GLPI['root_doc'], '/') . '/front/contract.form.php?id=';

                $select = "
                    (CASE
                        WHEN `" . PluginProjectbridgeEntity::$table_name . "`.`contract_id` IS NOT NULL
                            THEN CONCAT(
                                '<!--',
                                `glpi_contracts`.`name`,
                                '-->',

                                '<a href=\"" . $contract_link . "',
                                `" . PluginProjectbridgeEntity::$table_name . "`.`contract_id`,
                                '\">',
                                `glpi_contracts`.`name`,
                                '</a>'
                            )
                        ELSE
                            NULL
                    END)
                    AS `ITEM_" . $offset . "`,
                ";
            } else if ($key == 4202) {
                // url to ticket search for tickets in the entity that are not linked to a task
                $ticket_search_link = rtrim($CFG_GLPI['root_doc'], '/') . '/front/ticket.php?is_deleted=0&criteria[0][field]=4214&criteria[0][searchtype]=equals&criteria[0][value]=0&criteria[1][link]=AND&criteria[1][field]=80&criteria[1][searchtype]=equals&criteria[1][value]=';

                $select = "
                    CONCAT(
                        '<!--',
                        COALESCE(
                            ROUND(`unlinked_ticket_actiontimes`.`actiontime_sum`, 2),
                            0
                        ),
                        '-->',

                        '<a href=\"" . $ticket_search_link . "',
                        `glpi_entities`.`id`,
                        '\">',
                        COALESCE(
                            ROUND(`unlinked_ticket_actiontimes`.`actiontime_sum`, 2),
                            0
                        ),
                        '</a>'
                    )
                    AS `ITEM_" . $offset . "`,
                ";
            }

            break;

        case 'Ticket':
            if ($key == 4211) {
                // project name
                $project_link = rtrim($CFG_GLPI['root_doc'], '/') . '/front/project.form.php?id=';
                $select = "
                    GROUP_CONCAT(
                        DISTINCT CONCAT(
                            '<!--',
                            `glpi_projects`.`name`,
                            '-->',

                            '<a href=\"" . $project_link . "',
                            `glpi_projects`.`id`,
                            '\">',
                            `glpi_projects`.`name`,
                            '</a>'
                        )
                        SEPARATOR '$$##$$'
                    )
                    AS `ITEM_" . $offset . "`,
                ";
            } else if ($key == 4212) {
                // project task
                $task_link = rtrim($CFG_GLPI['root_doc'], '/') . '/front/projecttask.form.php?id=';
                $select = "
                    GROUP_CONCAT(
                        DISTINCT CONCAT(
                            '<!--',
                            `glpi_projecttasks`.`name`,
                            '-->',

                            '<a href=\"" . $task_link . "',
                            `glpi_projecttasks`.`id`,
                            '\">',
                            `glpi_projecttasks`.`name`,
                            '</a>'
                        )
                        SEPARATOR '$$##$$'
                    )
                    AS `ITEM_" . $offset . "`,
                ";
            } else if ($key == 4213) {
                // project task status

                $select = "
                    GROUP_CONCAT(DISTINCT `glpi_projectstates`.`name` SEPARATOR '$$##$$')
                    AS `ITEM_" . $offset . "`,
                ";
            } else if ($key == 4214) {
                // is the ticket linked to a task?
                $select = "
                    (CASE WHEN `glpi_projecttasks_tickets`.`tickets_id` = `glpi_tickets`.`id`
                    THEN
                        '1'
                    ELSE
                        '0'
                    END)
                    AS `ITEM_" . $offset . "`,
                ";
            } else if ($key == 4231) {
                // effective duration
                $onlypublicTasks = 1;
                $select = "
                    COALESCE(
                        ROUND(SUM( `glpi_tickettasks`.`actiontime`)/3600, 2),
                        0
                    )
                    AS `ITEM_" . $offset . "`,
                ";
                if ($onlypublicTasks) {
                    $select = "
                    COALESCE(
                        ROUND(SUM( CASE WHEN `glpi_tickettasks`.`is_private`= 0 THEN `glpi_tickettasks`.`actiontime` ELSE 0 END )/3600, 2),
                        0
                    )
                    AS `ITEM_" . $offset . "`,
                ";
                }
            }

            break;

        case 'Contract':
            if ($key == 4222) {
                // last task's status
                $task_link = rtrim($CFG_GLPI['root_doc'], '/') . '/front/projecttask.form.php?id=';
                $select = "
                    (CASE WHEN `last_tasks`.`project_task_id` IS NOT NULL
                    THEN
                        CONCAT(
                            '<!--',
                            COALESCE(`last_tasks`.`project_state`, '" . NOT_AVAILABLE . "'),
                            '-->',

                            '<a href=\"" . $task_link . "',
                            `last_tasks`.`project_task_id`,
                            '\">',
                            COALESCE(`last_tasks`.`project_state`, '" . NOT_AVAILABLE . "'),
                            '</a>'
                        )
                    ELSE
                        NULL
                    END)
                    AS `ITEM_" . $offset . "`,
                ";
            } else if ($key == 4221) {
                // project's name
                $project_link = rtrim($CFG_GLPI['root_doc'], '/') . '/front/project.form.php?id=';
                $select = "
                    (CASE WHEN `last_tasks`.`project_name` IS NOT NULL
                    THEN
                        CONCAT(
                            '<!--',
                            COALESCE(`last_tasks`.`project_name`, '" . NOT_AVAILABLE . "'),
                            '-->',

                            '<a href=\"" . $project_link . "',
                            `last_tasks`.`project_id`,
                            '\">',
                            COALESCE(`last_tasks`.`project_name`, '" . NOT_AVAILABLE . "'),
                            '</a>'
                        )
                    ELSE
                        NULL
                    END)
                    AS `ITEM_" . $offset . "`,
                ";
            }

            break;

        case 'projecttask':
            if ($key == 4231) {
                // effective duration
                $select = "
                    COALESCE(
                        ROUND(`ticket_actiontimes`.`actiontime_sum`, 2),
                        0
                    )
                    AS `ITEM_" . $offset . "`,
                ";
            } else if ($key == 4232) {
                // planned duration
                $select = "
                    COALESCE(
                        ROUND(`glpi_projecttasks`.`planned_duration` / 3600, 2),
                        0
                    )
                    AS `ITEM_" . $offset . "`,
                ";
            } else if ($key == 4233) {
                // last task in the project?

                $select = "
                    (CASE WHEN `glpi_projecttasks`.`id` = `last_tasks`.`id`
                    THEN
                        '" . __('Yes') . "'
                    ELSE
                        CASE WHEN `glpi_projecttasks`.`plan_end_date` IS NOT NULL
                        THEN
                            '" . __('No') . "'
                        ELSE
                            '" . NOT_AVAILABLE . "'
                        END
                    END)
                    AS `ITEM_" . $offset . "`,
                ";
            } else if ($key == 4234) {
                // project status

                $project_link = rtrim($CFG_GLPI['root_doc'], '/') . '/front/project.form.php?id=';

                $select = "
                    (CASE WHEN `glpi_projecttasks`.`projects_id` IS NOT NULL
                    THEN
                        CONCAT(
                            '<!--',
                            COALESCE(`states`.`name`, '" . NOT_AVAILABLE . "'),
                            '-->',

                            '<a href=\"" . $project_link . "',
                            `glpi_projecttasks`.`projects_id`,
                            '\">',
                            COALESCE(`states`.`name`, '" . NOT_AVAILABLE . "'),
                            '</a>'
                        )
                    ELSE
                        NULL
                    END)
                    AS `ITEM_" . $offset . "`,
                ";
            } else if ($key == 4235) {
                // project status
                $project_link = rtrim($CFG_GLPI['root_doc'], '/') . '/front/project.form.php?id=';

                $select = "
                    nb_tickets
                    AS `ITEM_" . $offset . "`,
                ";
            } else if ($key == 4236) {
                // percentage done
                $project_link = rtrim($CFG_GLPI['root_doc'], '/') . '/front/project.form.php?id=';
                $select = "
                    CONCAT(ROUND(
                        ROUND(`ticket_actiontimes`.`actiontime_sum`, 2)*100/ROUND(`glpi_projecttasks`.`planned_duration` / 3600, 2),
                        0
                    ),' %')
                    AS `ITEM_" . $offset . "`,
                ";
            }

            break;

        case 'Project':
            if ($key == 4231) {
                $select = "
                    (
                        COALESCE(
                            `task_counter`.`nb_tasks`,
                            0
                        )
                    ) AS `ITEM_" . $offset . "`,
                ";
            }

            break;

        default:
        // nothing to do
    }

    return $select;
}

/**
 * Add a custom left join to search
 *
 * @param string $itemtype
 * @param string $ref_table Reference table (glpi_...)
 * @param integer $new_table Plugin table
 * @param integer $linkfield
 * @param array $already_link_tables
 * @return string
 */
function plugin_projectbridge_addLeftJoin($itemtype, $ref_table, $new_table, $linkfield, $already_link_tables) {
    $left_join = "";

    switch ($new_table) {
        case PluginProjectbridgeEntity::$table_name:
            $left_join = "
                LEFT JOIN `" . $new_table . "`
                    ON (`" . $new_table . "`.`entity_id` = `" . $ref_table . "`.`id`)
                LEFT JOIN `glpi_contracts`
                    ON (`" . $new_table . "`.`contract_id` = `glpi_contracts`.`id`)
            ";

            break;

        case PluginProjectbridgeTicket::$table_name:
            $onlypublicTasks = PluginProjectbridgeConfig::getConfValueByName('CountOnlyPublicTasks');
            $wherePrivateCondition = '';
            $tableName = Ticket::getTable();
            if (!Session::haveRight("task", CommonITILTask::SEEPRIVATE) || $onlypublicTasks) {
                $tableName = TicketTask::getTable();
                $wherePrivateCondition = ' AND `' . $tableName . '`.`is_private` = 0 ';
            }
            if ($itemtype == 'Entity') {
                $left_join = "
                    LEFT JOIN (
                        SELECT
                            `glpi_tickets`.`entities_id`,
                            SUM(`" . $tableName . "`.`actiontime`) / 3600 AS `actiontime_sum`
                        FROM
                            `glpi_tickets`
                        LEFT OUTER JOIN `glpi_projecttasks_tickets`
                            ON (`glpi_tickets`.`id` = `glpi_projecttasks_tickets`.`tickets_id`)
                        INNER JOIN `glpi_tickettasks`
                            ON (`glpi_tickets`.`id` = `glpi_tickettasks`.`tickets_id`)    
                        WHERE TRUE
                            AND `glpi_tickets`.`is_deleted` = 0
                            AND `glpi_projecttasks_tickets`.`tickets_id` IS NULL
                            " . $wherePrivateCondition . " 
                        GROUP BY
                            `glpi_tickets`.`entities_id`
                    ) AS `unlinked_ticket_actiontimes`
                        ON (`unlinked_ticket_actiontimes`.`entities_id` = `" . $ref_table . "`.`id`)
                ";
            } else if ($itemtype == 'projecttask') {
                $left_join = "
                    LEFT JOIN (
                        SELECT
                            `glpi_projecttasks_tickets`.`projecttasks_id`,
                            SUM(`" . $tableName . "`.`actiontime`) / 3600 AS `actiontime_sum`, COUNT(*) as nb_tickets
                        FROM
                            `glpi_tickets`
                        INNER JOIN `glpi_projecttasks_tickets`
                            ON (`glpi_tickets`.`id` = `glpi_projecttasks_tickets`.`tickets_id`)
                        INNER JOIN `glpi_tickettasks`
                            ON (`glpi_tickets`.`id` = `glpi_tickettasks`.`tickets_id`)    
                        WHERE TRUE
                            AND `glpi_tickets`.`is_deleted` = 0
                            " . $wherePrivateCondition . "
                        GROUP BY
                            `glpi_projecttasks_tickets`.`projecttasks_id`
                    ) AS `ticket_actiontimes`
                        ON (`ticket_actiontimes`.`projecttasks_id` = `" . $ref_table . "`.`id`)
                    LEFT JOIN (
                        SELECT
                            `glpi_projecttasks`.`id`,
                            `glpi_projecttasks`.`projects_id`,
                            `glpi_projecttasks`.`plan_end_date`
                        FROM
                            `glpi_projecttasks`
                        INNER JOIN
                        (
                            /*
                              Get last task for each project
                             */
                            SELECT
                                `glpi_projecttasks`.`projects_id`,
                                MAX(`glpi_projecttasks`.`plan_end_date`) AS `plan_end_date`
                            FROM
                                `glpi_projecttasks`
                            WHERE TRUE
                            GROUP BY
                                `glpi_projecttasks`.`projects_id`
                        ) AS `max_end_dates`
                            ON (
                                `max_end_dates`.`projects_id` = `glpi_projecttasks`.`projects_id`
                                AND `max_end_dates`.`plan_end_date` = `glpi_projecttasks`.`plan_end_date`
                            )
                        WHERE TRUE
                        GROUP BY
                            `glpi_projecttasks`.`projects_id`
                    ) AS `last_tasks`
                        ON (`last_tasks`.`id` = `glpi_projecttasks`.`id`)
                    LEFT JOIN `glpi_projects` AS `projects`
                        ON (`projects`.`id` = `glpi_projecttasks`.`projects_id`)
                    LEFT JOIN `glpi_projectstates` AS `states`
                        ON (`states`.`id` = `projects`.`projectstates_id`)
                ";
            } else {
                $left_join = "
                    LEFT JOIN `glpi_projecttasks_tickets`
                        ON (`glpi_projecttasks_tickets`.`tickets_id` = `" . $ref_table . "`.`id`)
                    LEFT JOIN `glpi_projecttasks`
                        ON (`glpi_projecttasks`.`id` = `glpi_projecttasks_tickets`.`projecttasks_id`)
                    LEFT JOIN `glpi_projects`
                        ON (`glpi_projecttasks`.`projects_id` = `glpi_projects`.`id`)
                    LEFT JOIN `glpi_projectstates`
                        ON (`glpi_projectstates`.`id` = `glpi_projecttasks`.`projectstates_id`)
                    
                        
                ";
            }

            break;

        case PluginProjectbridgeContract::$table_name:
            if ($itemtype == 'Project') {
                $left_join = "
                    LEFT JOIN (
                        SELECT
                            `glpi_projecttasks`.`projects_id`,
                            COUNT(1) AS `nb_tasks`
                        FROM
                            `glpi_projecttasks`
                        WHERE TRUE
                        GROUP BY
                            `glpi_projecttasks`.`projects_id`
                    ) AS `task_counter`
                        ON (`task_counter`.`projects_id` = `glpi_projects`.`id`)
                ";
            } else {
                $left_join = "
                    LEFT JOIN `" . $new_table . "`
                        ON (`" . $new_table . "`.`contract_id` = `" . $ref_table . "`.`id`)
                    LEFT JOIN `glpi_projects`
                        ON (`" . $new_table . "`.`project_id` = `glpi_projects`.`id`)
                    LEFT JOIN (
                        SELECT
                            `glpi_projecttasks`.`projects_id` AS `project_id`,
                            `glpi_projecttasks`.`id` AS `project_task_id`,
                            `glpi_projectstates`.`name` AS `project_state`,
                            `glpi_projects`.`name` AS `project_name`
                        FROM
                            `glpi_projecttasks`
                        INNER JOIN (
                            /*
                              Get last task for each project
                             */
                            SELECT
                                `glpi_projecttasks`.`projects_id`,
                                MAX(`glpi_projecttasks`.`plan_end_date`) AS `plan_end_date`
                            FROM
                                `glpi_projecttasks`
                            WHERE TRUE
                            GROUP BY
                                `glpi_projecttasks`.`projects_id`
                        ) AS `max_end_dates`
                            ON (
                                `max_end_dates`.`projects_id` = `glpi_projecttasks`.`projects_id`
                                AND `max_end_dates`.`plan_end_date` = `glpi_projecttasks`.`plan_end_date`
                            )
                        INNER JOIN `glpi_projects`
                            ON (`glpi_projecttasks`.`projects_id` = `glpi_projects`.`id`)
                        LEFT JOIN `glpi_projectstates`
                            ON (`glpi_projectstates`.`id` = `glpi_projecttasks`.`projectstates_id`)
                        WHERE TRUE
                        GROUP BY `glpi_projecttasks`.`projects_id`
                    ) AS `last_tasks`
                        ON (`last_tasks`.`project_id` = `glpi_projects`.`id`)
                ";
            }

            break;

        default:
        // nothing to do
    }

    return $left_join;
}

/**
 * Add a custom where to search
 *
 * @param  string $link
 * @param  string $nott
 * @param  string $itemtype
 * @param  string $key
 * @param  string $val        Search argument
 * @param  string $searchtype Type of search (contains, equals, ...)
 * @return string
 */
function plugin_projectbridge_addWhere($link, $nott, $itemtype, $key, $val, $searchtype) {
    $where = "";
    global $DB;
    switch ($itemtype) {
        case 'Entity':
            if ($searchtype == 'contains') {
                if ($key == 4201) {
                    $where = $link . "`glpi_contracts`.`name` " . Search::makeTextSearch($DB->escape($val));
                } else {
                    $where = $link . "`unlinked_ticket_actiontimes`.`actiontime_sum` " . Search::makeTextSearch($DB->escape($val));
                }
            }

            break;

        case 'Ticket':
            if ($searchtype == 'contains') {
                if ($key == 4211) {
                    // project name
                    $where = $link . "`glpi_projects`.`name` " . Search::makeTextSearch($DB->escape($val));
                } else if ($key == 4212) {
                    // project task
                    $where = $link . "(`glpi_projecttasks`.`name` " . Search::makeTextSearch($DB->escape($val));
                    if (is_integer($val)) {
                        $where .= " OR `glpi_projecttasks`.`id`='" . $val . "'";
                    }

                    $where .= ")";
                } else if ($key == 4213) {
                    // project task status
                    $where = $link . "`glpi_projectstates`.`name` " . Search::makeTextSearch($DB->escape($val));
                }
            }
            if ($searchtype == 'equals') {
                if ($key == 4214) {
                    $searching_yes = (stripos('1', $val) !== false);
                    $searching_no = (stripos('0', $val) !== false);

                    $where_parts = [];

                    if ($searching_yes) {
                        $where_parts[] = "( `glpi_projecttasks_tickets`.`tickets_id` = `glpi_tickets`.`id` )";
                    }

                    if ($searching_no) {
                        $where_parts[] = "( `glpi_projecttasks_tickets`.`tickets_id` IS NULL )";
                    }

                    if (empty($where_parts)) {
                        $where_parts[] = "TRUE";
                    }

                    $where = $link . "(" . implode(' OR ', $where_parts) . ")";
                }
            }

            break;

        case 'Contract':
            if ($searchtype == 'contains') {
                if ($key == 4222) {
                    // project task status

                    $where_parts = [
                        "`last_tasks`.`project_state` " . Search::makeTextSearch($DB->escape($val)),
                    ];

                    if (stripos(NOT_AVAILABLE, $val) !== false) {
                        $where_parts[] = "(
                            `last_tasks`.`project_task_id` IS NOT NULL
                            AND `last_tasks`.`project_state` IS NULL
                        )";
                    }

                    $where = $link . "(" . implode(' OR ', $where_parts) . ")";
                } else if ($key == 4221) {
                    // project task status

                    $where_parts = [
                        "`last_tasks`.`project_name` " . Search::makeTextSearch($DB->escape($val)),
                    ];

                    if (stripos(NOT_AVAILABLE, $val) !== false) {
                        $where_parts[] = "(
                            `last_tasks`.`project_id` IS NOT NULL
                            AND `last_tasks`.`project_name` = ''
                        )";
                    }

                    $where = $link . "(" . implode(' OR ', $where_parts) . ")";
                }
            }

            break;

        case 'projecttask':
            if ($searchtype == 'contains') {
                if ($key == 4231) {
                    $where = $link . "`ticket_actiontimes`.`actiontime_sum` " . Search::makeTextSearch($DB->escape($val));
                } else if ($key == 4232) {
                    $where = $link . " ROUND(`glpi_projecttasks`.`planned_duration` / 3600, 2) " . Search::makeTextSearch($DB->escape($val));
                } else if ($key == 4233) {
                    $searching_yes = (stripos(__('Yes'), $val) !== false);
                    $searching_no = (stripos(__('No'), $val) !== false);
                    $searching_not_available = (stripos(NOT_AVAILABLE, $val) !== false);

                    $where_parts = [];

                    if ($searching_yes) {
                        $where_parts[] = "( `glpi_projecttasks`.`id` = `last_tasks`.`id` )";
                    }

                    if ($searching_no) {
                        $where_parts[] = "(
                            `glpi_projecttasks`.`plan_end_date` IS NOT NULL
                            AND `last_tasks`.`id` IS NULL
                        )";
                    }

                    if ($searching_not_available) {
                        $where_parts[] = "( `glpi_projecttasks`.`plan_end_date` IS NULL )";
                    }

                    if (empty($where_parts)) {
                        $where_parts[] = "TRUE";
                    }

                    $where = $link . "(" . implode(' OR ', $where_parts) . ")";
                } else if ($key == 4234) {
                    $where_parts = [
                        "(
                            `glpi_projecttasks`.`projects_id` IS NOT NULL
                            AND `states`.`name` " . Search::makeTextSearch($DB->escape($val)) . "
                        )",
                    ];

                    if (stripos(NOT_AVAILABLE, $val) !== false) {
                        $where_parts[] = "(
                            `glpi_projecttasks`.`projects_id` IS NOT NULL
                            AND `states`.`name` IS NULL
                        )";
                    }

                    $where = $link . "(" . implode(' OR ', $where_parts) . ")";
                }
            }

            break;

        case 'Project':
            if ($searchtype == 'contains') {
                if ($key == 4231) {
                    // number of projects

                    if ($val == 0) {
                        $where = $link . "`task_counter`.`nb_tasks` IS NULL";
                    } else {
                        $where = $link . "`task_counter`.`nb_tasks` " . Search::makeTextSearch($DB->escape($val));
                    }
                }
            }

            break;

        default:
        // nothing to do
    }

    return $where;
}

/**
 * Add massive action options
 *
 * @param  string $type
 * @return array
 */
function plugin_projectbridge_MassiveActions($type) {
    $massive_actions = [];

    switch ($type) {
        case 'Ticket':
            $massive_actions['PluginProjectbridgeTicket' . MassiveAction::CLASS_ACTION_SEPARATOR . 'deleteProjectLink'] = __('Delete the link with any project task', 'projectbridge');
            //            $massive_actions['PluginProjectbridgeTicket' . MassiveAction::CLASS_ACTION_SEPARATOR . 'addProjectLink'] = __('Link to a project', 'projectbridge');
            //            $massive_actions['PluginProjectbridgeTicket' . MassiveAction::CLASS_ACTION_SEPARATOR . 'addProjectTaskLink'] = __('Force link to a project task', 'projectbridge');
            $massive_actions['PluginProjectbridgeTicket' . MassiveAction::CLASS_ACTION_SEPARATOR . 'addProjectTaskLink'] = __('Force link to a project task', 'projectbridge');

            break;

        default:
        // nothing to do
    }

    return $massive_actions;
}

function plugin_projectbridge_giveItem($type, $ID, $data, $num) {
    global $CFG_GLPI, $DB;
    if ($num == "projecttask_4235") {
        $projectTaskId = $data['raw']['id'];
        // calcul nombre tickets associés à la tâche de projet
        $pluginProjectbridgeContract = new PluginProjectbridgeContract();
        $nbTickets = $pluginProjectbridgeContract->getNbTicketsAssociateToProjectTask($projectTaskId);
        $ticket_search_link = rtrim($CFG_GLPI['root_doc'], '/') . '/front/ticket.php?is_deleted=0&criteria[0][field]=4212&criteria[0][searchtype]=contains&criteria[0][value]=' . $projectTaskId . '';
        return '<a href="' . $ticket_search_link . '">' . $nbTickets . '</a>';
    }
}
