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

if (($track->fields['status'] == Ticket::CLOSED) && $satisfaction->getFromDB($ticket_satisfaction->ticket_id)) {
    if (!is_null($satisfaction->fields['date_answered'])) {
        echo "<p class='center b'>" . __('Pesquisa já respondida!') . "</p>";
        exit;
    }
} else {
    echo "<p class='center b'>" . __('No generated survey') . "</p>";
    exit;
}

$data = [];
$data['satisfaction'] = $ticket_satisfaction->star;
$data['tickets_id'] = $ticket_satisfaction->ticket_id;
$satisfaction->getFromDB($ticket_satisfaction->ticket_id);
$satisfaction->update($data);

echo "<p class='center b'>" . __('Pesquisa respondida com sucessoo!') . "</p>";
