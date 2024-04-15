<?php

namespace App\Serializers;

use PHPHtmlParser\Dom;
use App\Enums\ActivityType;
use App\Models\Activity;
use App\Interfaces\rosterSerializer;


class rosterBuster implements rosterSerializer
{

    public function parseRoster(string $source): array
    {
        $dom = new Dom;
        $dom->loadStr($source);

        $dateSelector = $dom->find('#ctl00_Main_periodSelect option[selected]');
        $dateRange = explode('|', $dateSelector->getAttribute('value'));
        $baseDate = substr($dateRange[0], 0, 8); //TODO - make this a function, handle month change

        $activity_table = $dom->find('table.activityTableStyle', 0);
        $activity_rows = $activity_table->find('tr');

        /** @var Activity[] $activities */
        $activities = [];
        $current_day = null;
        foreach ($activity_rows as $tr) {
            if (is_a($tr, Dom\HtmlNode::class)) {
                if ($tr->getAttribute('class') == 'activity-table-header') {
                    continue;
                }
                $row = $this->parseRosterRowToArray($tr);

                if ($row['date']) {
                    $aDate = explode(' ', $row['date']);
                    $current_day = $baseDate . $aDate[1];
                }
                if ($current_day === null) {
                    continue;
                } //all events should have a date, or be ignored

                if ($row['checkinutc']) {
                    $activities[] = new Activity([
                        'type' => ActivityType::CHECK_IN,
                        'starts' => $this->hoursToFullDate($row['checkinutc'], $current_day),
                        'activity_remark' => $row['activityRemark'],
                        'from' => $row['fromstn'],
                    ]);
                }
                if ($row['checkoututc']) {
                    $activities[] = new Activity([
                        'type' => ActivityType::CHECK_OUT,
                        'starts' => $this->hoursToFullDate($row['checkoututc'], $current_day),
                        'activity_remark' => $row['activityRemark'],
                        'from' => $row['fromstn'],
                    ]);
                }

                $activity_code = $row['activity'];
                if ($activity_code == 'OFF') {
                    //TODO - consider merge multiple day off events into one
                    $activities[] = new Activity([
                        'type' => ActivityType::DAY_OFF,
                        'starts' => $this->hoursToFullDate($row['stdutc'], $current_day),
                        'ends' => $this->hoursToFullDate($row['stautc'], $current_day),
                        'from' => $row['fromstn'],
                        'to' => $row['tostn'], //probably redundant
                    ]);
                } elseif ($activity_code == 'SBY') {
                    $activities[] = new Activity([
                        'type' => ActivityType::STAND_BY,
                        'starts' => $this->hoursToFullDate($row['stdutc'], $current_day),
                        'ends' => $this->hoursToFullDate($row['stautc'], $current_day),
                        'from' => $row['fromstn'],
                        'to' => $row['tostn'], //probably redundant
                    ]);
                } elseif ((preg_match("/[A-Z]{2}\d{2}/", $activity_code))) {
                    $activities[] = new Activity([
                        'type' => ActivityType::FLIGHT,
                        'starts' => $this->hoursToFullDate($row['stdutc'], $current_day),
                        'ends' => $this->hoursToFullDate($row['stautc'], $current_day),
                        'from' => $row['fromstn'],
                        'to' => $row['tostn'],
                        'flight' => $row['activity'],
                        'activity_remark' => $row['activityRemark'],
                    ]);
                } else {
                    $activities[] = new Activity([
                        'type' => ActivityType::UNKNOWN,
                        'starts' => $this->hoursToFullDate($row['stdutc'], $current_day),
                        'ends' => $this->hoursToFullDate($row['stautc'], $current_day),
                        'from' => $row['fromstn'],
                        'to' => $row['tostn'], //probably redundant
                        'activity_remark' => $row['activityRemark'],
                        'row' => json_encode($row),
                    ]);
                }
            }
        }
        return $activities;
    }

    private function hoursToFullDate(string $hours, string $current_day)
    {
        if ($hours == '2300-1') { //next day midnight notation handle. Possible bug when there will be something like 2311-
            $current_day = date('Ymd', strtotime($current_day . ' +1 day'));
            $hours = '0000';
        }elseif( !preg_match('/^\d{4}$/', $hours) ){
            throw new \Exception('Invalid hours format: ' . $hours);
        }
        $hh = substr($hours, 0, 2);
        $mm = substr($hours, 2, 2);
        return $current_day . ' ' . $hh . ':' . $mm . ':00';
    }

    private function parseRosterRowToArray(Dom\HtmlNode $tr): array
    {
        $ret = [
            'date' => $this->extractText('td.activitytablerow-date', $tr),
            //date in format 'Mon 10'
            'dc' => $this->extractText('td.activitytablerow-dc', $tr),
            //?? for CAR activity set to DH
            'checkinutc' => $this->extractText('td.activitytablerow-checkinutc', $tr),
            //checkin time in UTC
            'checkoututc' => $this->extractText('td.activitytablerow-checkoututc', $tr),
            //checkout time in UTC
            'activity' => $this->extractText('td.activitytablerow-activity', $tr),
            //activity code
            'activityRemark' => $this->extractText('td.activitytablerow-activityRemark', $tr),
            //activity code
            'fromstn' => $this->extractText('td.activitytablerow-fromstn', $tr),
            //from station
            'stdutc' => $this->extractText('td.activitytablerow-stdutc', $tr),
            //std time in UTC
            'tostn' => $this->extractText('td.activitytablerow-tostn', $tr),
            //to station
            'stautc' => $this->extractText('td.activitytablerow-stautc', $tr),
            //sta time in UTC
            //'hotel' => $this->extractText('td.activitytablerow-AC',$tr), //AC/Hotel code - no other example than DO4 (some Operate)
            'blockhours' => $this->extractText('td.activitytablerow-blockhours', $tr),
            //block hours
            'duration' => $this->extractText('td.activitytablerow-duration', $tr),
            //duration, works for flights, but 3.00 for standby
            //'counter1' => $this->extractText('td.activitytablerow-counter1',$tr), //Ext, empty or zero
            //'paxbooked' => $this->extractText('td.activitytablerow-Paxbooked',$tr), //pax booked (always empty
            //'tailnumber' => $this->extractText('td.activitytablerow-Tailnumber',$row), //tail number, but there is only OYJRY

        ];
        return $ret;
    }

    private function extractText(string $selector, Dom\HtmlNode $row)
    {
        $element = $row->find($selector, 0);
        if (is_null($element)) {
            return null;
        }
        $txt = $element->text(true);
        return trim(strip_tags(html_entity_decode($txt)), " \t\n\r\0\x0B\xC2\xA0");
    }

    private function rosterDiscriminator()
    {
        //"https://air-traffic-control.rosterbuster.aero/rosterbuster/

    }
}
