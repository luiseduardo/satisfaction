<?php

/**
 * ---------------------------------------------------------------------
 * GLPI - Gestionnaire Libre de Parc Informatique
 * Copyright (C) 2015-2018 Teclib' and contributors.
 *
 * http://glpi-project.org
 *
 * based on GLPI - Gestionnaire Libre de Parc Informatique
 * Copyright (C) 2003-2014 by the INDEPNET Development Team.
 *
 * ---------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of GLPI.
 *
 * GLPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GLPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GLPI. If not, see <http://www.gnu.org/licenses/>.
 * ---------------------------------------------------------------------
 */

use Glpi\Event;

global $CFG_GLPI;

include('../../inc/includes.php');
$ticket_satisfaction = json_decode(base64_decode($_GET['t']));

Html::includeHeader();

$track = new Ticket();
$satisfaction = new TicketSatisfaction();

if (!$track->getFromDB($ticket_satisfaction->ticket_id)) {
    Html::displayNotFoundError();
    exit;
}

if ($_POST) {

    if ($satisfaction->getFromDB($ticket_satisfaction->ticket_id)) {
        if (!is_null($satisfaction->fields['comment'])) {
            echo "<p class='center b'>" . __('Pesquisa já respondida!') . "</p>";
            exit;
        }
    }

    $satisfaction->getFromDB($ticket_satisfaction->ticket_id);
    $satisfaction->update($_POST);

    echo "<p class='center b'>" . __('Seus comentários foram adicionados a pesquisa!') . "</p>";
} else {

    // save satisfaction
    if ($satisfaction->getFromDB($ticket_satisfaction->ticket_id)) {
        if (is_null($satisfaction->fields['date_answered'])) {
            $data = [];
            $data['satisfaction'] = $ticket_satisfaction->star;
            $data['tickets_id'] = $ticket_satisfaction->ticket_id;
            $satisfaction->getFromDB($ticket_satisfaction->ticket_id);
            $satisfaction->update($data);
        }
    }
?>
    <div id="page">
        <form name="form" method="post" action="<?php echo $CFG_GLPI["url_base"] ?>/plugins/satisfaction/apisatisfaction.php?t=<?php echo $_GET['t'] ?>" enctype="multipart/form-data">
            <input type="hidden" name="plugin_satisfaction_surveys_id" value="<?php echo $ticket_satisfaction->satisfaction_id ?>">
            <input type="hidden" name="tickets_id" value="<?php echo $ticket_satisfaction->ticket_id ?>">
            <div class="spaced" id="tabsbody">
                <table class="tab_cadre_pager">
                    <tbody>
                        <tr class="tab_bg_2">
                            <td class="b big"><span class="status"><i class="itilstatus fas fa-circle closed" title="Fechado"></i></span>Ticket <?php echo $ticket_satisfaction->ticket_id ?></td>
                        </tr>
                    </tbody>
                </table>
                <table class="tab_cadre_fixe" id="mainformtable">
                    <tbody>
                        <tr class="headerRow">
                            <th colspan="2" class="">
                                Pesquisa de Satisfação<br>
                                <small>Pesquisa respondida com sucesso! caso julgue necessário informe seu comentário abaixo e clique em "Salvar"</small>
                            </th>
                        </tr>
                        <tr class="tab_bg_2">
                            <td>Comentários</td>
                            <td><textarea cols="45" rows="7" name="comment"></textarea></td>
                        </tr>
                        <tr>
                            <td class="center" colspan="2"><input type="submit" value="Salvar" name="update" class="submit"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php
            Html::closeForm();
            ?>
    </div>
<?php
}
