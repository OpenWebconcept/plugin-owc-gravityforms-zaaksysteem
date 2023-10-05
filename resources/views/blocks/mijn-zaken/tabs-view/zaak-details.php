<?php

declare(strict_types=1);

use function OWC\Zaaksysteem\Foundation\Helpers\view;

$zaak = $vars['zaak'];
$label = 'Status';
$status = $zaak->status->statustoelichting ?? false;
if ($zaak->resultaat) {
    $label = 'Resultaat';
    $status = $zaak->resultaat->toelichting;
}
$status = $status ?: 'Onbekend';

?>

<div class="zaak-details">
    <h2>Details</h2>
    <table class="zaak-details-table">
        <tr>
            <th>Registratiedatum</th>
            <td><?php echo $zaak->registratiedatum->format('j F Y'); ?> </td>
            <td><a href="#">Bekijk originele aanvraag</a></td>
        </tr>
        <tr>
            <th>Startdatum</th>
            <td><?php echo $zaak->startdatum->format('j F Y'); ?></td>
        </tr>
        <tr>
            <th>Zaaknummer</th>
            <td><?php echo $zaak->identificatie; ?></td>
        </tr>
        <tr>
            <th>Status</th>
            <td><?php echo $status; ?></td>
        </tr>
    </table>
</div>