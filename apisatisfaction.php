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
}

if ($_POST) {

    $satisfaction->getFromDB($ticket_satisfaction->ticket_id);
    $satisfaction->update($_POST);

    echo "<p class='center b'>" . __('Obrigado pela sua participação!') . "</p>";
} else {

    if (($track->fields['status'] == Ticket::CLOSED) && $satisfaction->getFromDB($ticket_satisfaction->ticket_id)) {
        if (!is_null($satisfaction->fields['date_answered'])) {
            echo "<p class='center b'>" . __('Pesquisa já respondida!') . "</p>";
        } else {
?>
            <style>
                <?php
                //std cache, with DB connection
                include_once GLPI_ROOT . "/inc/db.function.php";
                include_once GLPI_ROOT . '/inc/config.php';

                echo Html::compileScss($_GET);
                ?>.rate {
                    float: left;
                    height: 46px;
                    padding: 0 10px;
                }

                .rate:not(:checked)>input {
                    position: absolute;
                    top: -9999px;
                }

                .rate:not(:checked)>label {
                    float: right;
                    width: 1em;
                    overflow: hidden;
                    white-space: nowrap;
                    cursor: pointer;
                    font-size: 30px;
                    color: #ccc;
                }

                .rate:not(:checked)>label:before {
                    content: '★ ';
                }

                .rate>input:checked~label {
                    color: #ffc700;
                }

                .rate:not(:checked)>label:hover,
                .rate:not(:checked)>label:hover~label {
                    color: #deb217;
                }

                .rate>input:checked+label:hover,
                .rate>input:checked+label:hover~label,
                .rate>input:checked~label:hover,
                .rate>input:checked~label:hover~label,
                .rate>label:hover~input:checked~label {
                    color: #c59b08;
                }

                /* Modified from: https://github.com/mukulkant/Star-rating-using-pure-css */
            </style>
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
                                    <th colspan="2" class="">Pesquisa de Satisfação</th>
                                </tr>
                                <tr class="tab_bg_2">
                                    <td>Satisfação com a solução do chamado</td>
                                    <td>
                                        <div class="rate">
                                            <input type="radio" id="star5" name="satisfaction" value="5" />
                                            <label for="star5" title="text">5 stars</label>
                                            <input type="radio" id="star4" name="satisfaction" value="4" />
                                            <label for="star4" title="text">4 stars</label>
                                            <input type="radio" id="star3" name="satisfaction" value="3" />
                                            <label for="star3" title="text">3 stars</label>
                                            <input type="radio" id="star2" name="satisfaction" value="2" />
                                            <label for="star2" title="text">2 stars</label>
                                            <input type="radio" id="star1" name="satisfaction" value="1" />
                                            <label for="star1" title="text">1 star</label>
                                        </div>
                                    </td>
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
            Html::displayMessageAfterRedirect();
        }
    } else {
        echo "<p class='center b'>" . __('No generated survey') . "</p>";
    }
}
