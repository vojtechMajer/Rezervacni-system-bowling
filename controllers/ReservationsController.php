<?php

class ReservationsController extends Controller
{
    function __construct()
    {
        $this->dontShowLayout = true;
    }

    public function load($parameters)
    {
        // TEST REZERVACE

        // $timeS = mktime(17, 30, 0, 5, 13, 2024);
        // $timeE = mktime(18, 00, 0, 5, 13, 2024);

        // Add reservation 
        // ReservationManager::addReservation (
        //     LaneManager::getLaneById(1),
        //     ReservationType::getReservationTypesById(1),
        //     date("Y-m-d H:i:s", mktime(19, 30, 0, 5, 14, 2024)),
        //     date("Y-m-d H:i:s", mktime(18, 00, 0, 5, 14, 2024))
        // );

        switch ($parameters[0]) {
            // příklad url: http://localhost/reservations/in-week?week=2024-05-26
            case 'in-week':
                if (isset($_GET["week"])) {
                    $reservations = ReservationManager::getReservationsInWeek(date('Y-m-d', strtotime($_GET['week'])));
                    // zakodování dat do JSON formátu data se pak v pohledu vypíší
                    $this->data["reservationsJSON"] = json_encode($reservations);
                }
                // v případě špatně zadaného parametru week
                else {
                    $this->data["reservationsJSON"] = "[]";
                }
                break;
            // příklad url: http://localhost/reservations/by-date?date=2024-05-26
            case 'by-day':
                if (isset($_GET["day"])) {
                    $reservations = ReservationManager::getReservationsOnDate(date('Y-m-d', strtotime($_GET['day'])));
                    // $this->data["reservations"] = $reservations;
                    $this->data["reservationsJSON"] = json_encode($reservations);
                } else {
                    $this->data["reservationsJSON"] = "[]";
                }
                break;

            case 'all':
                $reservations = ReservationManager::getAllReservations();
                // $this->data["reservations"] = $reservations;
                $this->data["reservationsJSON"] = json_encode($reservations);
                break;

            default:
                $this->data["reservationsJSON"] = "[]";
                break;
        }

        // TEST
        // $this->data["parametry"] = $parameters;
        // $this->data["testDate"] = date('Y-m-d', strtotime($_GET['week']));
        $this->view = "reservations";
    }
}

// SELECT * 
// FROM reservation 
// 			-- Start rezervace												  	end rezervace
// WHERE start > STR_TO_DATE('2023-04-02 16:00:00', '%Y-%m-%d %H:%i:%s') AND start < STR_TO_DATE('2023-04-02 16:30:00', '%Y-%m-%d %H:%i:%s') ;


// TEST
// $lane = LaneManager::getLaneById(1);
// $overlapingReservations = ReservationManager::getOverlapingReservations('2024-04-02 15:00:00', '2024-04-02 17:59:00', $lane);
