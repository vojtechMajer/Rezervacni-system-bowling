<?php
class OrderManager
{
    private static function queryToOrders($query)
    {
        $orders = array();
        foreach ($query as $orderQuery) {
            $lane = LaneManager::getLaneById($orderQuery["id_lane"]);
            $reservation = ReservationManager::getReservationById($orderQuery["id_reservation"]);

            $orders[] =
                new Order($orderQuery["id_order"], $reservation, $orderQuery["id_user"]);
        }
        return $orders;
    }

    public static function getAllOrders()
    {
        $orderQuery = Db::queryAll("select * from orders");
        $reservations = OrderManager::queryToOrders($orderQuery);
        return $reservations;
    }

    public static function getAllOrdersFromUser($userId)
    {
        $orderQuery = Db::queryAll("select * from orders where id_user = ?", [$userId]);

        $reservations = OrderManager::queryToOrders($orderQuery);
        return $reservations;
    }

    //     /**
//      * date format is yyy-mm-dd
//      */
//     public static function getReservationsAfterDate($date)
//     {
//         $reservationsQuery = Db::queryAll("select * from reservation where YEARWEEK(start, 1) = YEARWEEK( STR_TO_DATE('$date', '%Y-%m-%d'), 1)");
//         $reservations = ReservationManager::queryToReservations($reservationsQuery);

    //         return $reservations;
//     }


    //     /**
//      * date format is yyy-mm-dd
//      */
//     public static function getReservationsInWeek($date)
//     {
//         $reservationsQuery = Db::queryAll(" SELECT *
//             FROM   reservation
//             WHERE  YEARWEEK(start, 1) = YEARWEEK(?, 1)",
//             [$date]
//         );
//         // Array rezervací
//         $reservations = array();

    //         // Přidání objektů Rezervací do vytvořeného pole 
//         foreach ($reservationsQuery as $reservationQuery) {
//             $lane = LaneManager::getLaneById($reservationQuery["id_lane"]);
//             $reservations[] =
//                 new Reservation($reservationQuery["id_reservation"], $reservationQuery["id_reservation_type"], $lane, $reservationQuery["start"], $reservationQuery["end"]);
//         }
//         return $reservations;
//     }

    //     public static function getReservationsOnDate($date)
//     {
//         $reservationsQuery = Db::queryAll(" SELECT *
//             FROM   reservation
//             WHERE  Date(start) = STR_TO_DATE(?, '%Y-%m-%d')",
//             [$date]
//         );

    //         $reservations = array();
//         foreach ($reservationsQuery as $reservationQuery) {
//             $lane = LaneManager::getLaneById($reservationQuery["id_lane"]);

    //             $reservations[] =
//                 new Reservation($reservationQuery["id_reservation"], $reservationQuery["id_reservation_type"], $lane, $reservationQuery["start"], $reservationQuery["end"]);
//         }

    //         return $reservations;
//     }


    //     public static function addReservation(Lane $lane, ReservationType $reservationType, $startDate, $endDate)
//     {
//         $sql = "insert into reservation (id_reservation_type, id_lane, start, end) VALUES (?, ?, ?, ?)";
//         Db::query($sql, [$reservationType->id, $lane->id, $startDate, $endDate]);

    //         $id = Db::lastInsertId();
//         return new Reservation($id, $reservationType, $lane, $startDate, $endDate);
//     }

    //     public static function createOrder(Reservation $reservation, $userId)
//     {
//         $sql = "insert into orders (id_reservation, id_user) VALUES (?, ?)";

    //         Db::query($sql, [$reservation->id, $userId]);
//         $id = Db::lastInsertId();

    //         return new Order($id, $reservation, $userId);
//     }

    //     public static function reservationOverlaps($startDate, $endDate, Lane $lane)
//     {
//         $sql = "SELECT * 
//         FROM reservation
//         WHERE (
//             start < STR_TO_DATE(?, '%Y-%m-%dT%H:%i') 
//             AND end > STR_TO_DATE(?, '%Y-%m-%dT%H:%i')
//         ) 
//         AND id_lane = ?; 
//         ";

    //         $query = Db::queryAll($sql, [$endDate, $startDate, $lane->id]);

    //         return (empty($query) ? false : true);
//     }

    //     public static function getReservationTypes()
//     {
//         $reservationTypes = array();
//         $query = Db::queryAll("select * from reservation_type");

    //         foreach ($query as $reservationTypeRow) {
//             $reservationTypes[] = new ReservationType($reservationTypeRow["id_reservation_type"], $reservationTypeRow["name"]);
//         }

    //         return $reservationTypes;
//     }
}
// insert into reservation(id_reservation_type, id_reservation, id_lane, start, end) VALUES (1, 1, "2024-04-23 14:00:01", "2024-04-23 14:00:01")

// 2024-06-09T14:30
// 2024-06-09T16:30