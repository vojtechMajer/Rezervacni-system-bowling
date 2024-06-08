$(() => {
    // +-----------------------------+
    // |            SetUp            |
    // +-----------------------------+
    
    // --- Nastaveni constant + potrebne promene ---
    
    const SetUp = new Map([                                         //Objekt Map() obsahujici vsechna nastaveni (v budoucnu dotaz na server)
        ["drahyUrl","http://localhost/lanes"],                          // JSON url pro zjisteni poctu drah
        ["rezervaceUrl","http://localhost/reservations/by-day?day="],   // JSON url pro zjisteni jiz existujicich rezervaci
        ["StartTime", 8],                                               //zacatek oteviraci doby format: 8 => 8:00
        ["EndTime", 18.5]                                               //konec oteviraci doby format: 18.5 => 18:30
    ])

    let SelectedReservationArray = [];   //Array pro ukladani vybranych rezervaci

    const reservationTable = $("table#reservation-table");  // Table pro tabulku
    if (reservationTable.length === 0) {                    //overeni existence tabulky
        console.log("Table not found!");                    //Error hlaska
        return;                                             //preruseni celeho JS
    }
 
    const Today = new Date().toISOString().split('T')[0];   //Aktualni datum
    const DateInput = $("input#date");                      //Input:date pro urceni datumu rezervace tabulku
    if (DateInput.length === 0) {                           //overeni existence Input:date
        console.log("Intup#date not found!");               //Error hlaska
        return;                                             //preruseni celeho JS
    } else {
        DateInput.val(Today);                                                                                   //nastaveni na momentalni datum
        DateInput.on("change", function (){                                                                     //pridani eventu on:change pro detekci manualniho zadani data uzivatelem
            let dateSplit = DateInput.val().split('-');                                                         //rozdeleni hodnoty v inputu na dny mesice a roky
            let todaySplit = Today.split('-');                                                                  //rozdeleni dnesniho data na dny mesice a roky
            if(dateSplit[0] > todaySplit[0] || dateSplit[1] > todaySplit[1] || dateSplit[2] > todaySplit[2]) {  //porovnani zda datum v inputu je vetsi nez dnesni datum
                DrawTable(reservationTable,SetUp,DateInput,SelectedReservationArray);                           //pokud ano -> znovu nakresleni tabulky
            } else {                                                                                            
                DateInput.val(Today);                                                                           //pokud ne -> nastaveni data v inputu na dnesni datum
            }
        })
    }

    const DateNext = $("button#date-next");                                         //Tlacitko pro rychle posouvani datumu doperdu
    if (DateNext.length === 0) {                                                    //overeni existence tlacitka
        console.log("button#date-next not found!");                                 //Error hlaska
        return;                                                                     //preruseni celeho JS
    } else {
        DateNext.on("click", function() {                                           //pridani event on:click
            addDay(1, DateInput);                                                   //posunuti data o jeden den dopredu
            DrawTable(reservationTable,SetUp,DateInput,SelectedReservationArray);   //Znovu nakresleni tabulky
        });
    }
    

    const DatePrev = $("button#date-prev");                                             //Tlacitko pro rychle posouvani datumu dozadu
    if (DatePrev.length === 0) {                                                        //overeni existence tlacitka
        console.log("button#date-prev not found!");                                     //Error hlaska
        return;                                                                         //preruseni celeho JS
    } else {
        DatePrev.on("click", function() {                                               //pridani event on:click
            if(DateInput.val() !== Today) {                                             //overeni ze se uzivaten nestazi vytvorit rezervaci v minulosti
                addDay(-1, DateInput);                                                  //posunuti data o jeden den dozadu
                DrawTable(reservationTable,SetUp,DateInput,SelectedReservationArray);   //Znovu nakresleni tabulky
            }
        });
    }

    DrawTable(reservationTable,SetUp,DateInput,SelectedReservationArray);            //Tvorba tabulky
})

    // +---------------------------------+
    // |            Functions            |
    // +---------------------------------+

function ISOdateToHours(date) {                                         //funkce prevadi ISO format data na ciste hodiny
    let [datePart, timePart] = date.split(' ');                         //oddeleni datumu od casu
    let [hours, minutes, seconds] = timePart.split(':').map(Number);    //oddeleni hodin,minut,sekund
    let decimalTime = hours + minutes / 60;                             //spojeni hodin a minut na hodiny
    return decimalTime;
}

function addDay(days, dateInput){                       //Funkce zajistujici pridani x dnu do input:datum 
    let date = new Date(dateInput.val());               //zjisteni aktualni hodnoty inputu
    date.setDate(date.getDate() + days);                //pridani jednoho dnu
    dateInput.val(date.toISOString().split('T')[0]);    //zapsani nove hodnoty do inputu
}




function DrawTable(reservationTable, setUp, date, data) {
// --- Code ---
    reservationTable.empty();   //vyprazdneni tabulky

    const tableHeader = $("<tr>"); // zacatek hlavicky
    tableHeader.appendTo(reservationTable);
    tableHeader.append("<th>DRAHY\n/ČASY");
    for(let i = setUp.get("StartTime"); i <= setUp.get("EndTime"); i += 0.5) {
        let min = (i == Math.floor(i)) ? "00" : "30";               //kontrola pro 30min interval
        tableHeader.append("<th> " + Math.floor(i) + ":" + min + "</th>");
    }
    reservationTable.append("</tr>")

    // --- dotaz na JSON, pocet drah a generovani jednotlivych tlacitek ---

    $.getJSON(setUp.get("drahyUrl"), function (lanes) {
        $.each(lanes, function (i, lane) {
            // hlavicka drahy
            const laneRow = $("<tr>");
            laneRow.appendTo(reservationTable);
            laneRow.append("<th>" + lane.id + ". line" + "</th>");
            // generovani tlacitek pro samotnou rezervaci
            for(let i = setUp.get("StartTime"); i < setUp.get("EndTime"); i += 0.5) {
                laneRow.append('<td><button id="' + lane.id + '-' + i*10 + '"></button>');
            }
            reservationTable.append("</tr>")
        })

        // --- dotaz na JSON, prirazeni rezervaci k tlacitkum, jejich deaktivace/aktivace ---
        let datePart = date.val().split('-');                          //rozdeleni dat z input:date na den mesic rok
        let url = setUp.get("rezervaceUrl") + datePart[0] + datePart[1] + datePart[2];   //priprava url pro JSON dotaz
        $.getJSON(url, function (rezervations) {
            $.each(rezervations, function (i, reservation) {
                for( i = ISOdateToHours(reservation.startDate); i < ISOdateToHours(reservation.endDate); i += 0.5) {
                    let button = "button#" + reservation.lane.id + "-" + i*10  //priprava pro nalezeni tlacitka
                    $(button).text("X").addClass('reserved');               //nalezeni tlacitka a oznaceni tlacitka jako rezervovano
                }
            })
        })

        //Sprovozneni nezablokovanych tlacitek
        for(let i = 1; i <= lanes.length; i ++) {                                               //for loop ktery zajisti ze kazda draha bude testovana
            for(let j = setUp.get("StartTime"); j < setUp.get("EndTime"); j += 0.5) {           //for loop ktery zajisti test kazdeho tlacitka v draze
                let button = "button#" + i + "-" + j*10;                                        //promena s identifikatorem aktualniho tlacitka
                if(data.indexOf(date.val() + "-" + i + "-" + j*10) != -1) {                     //kontrola zda se aktualni tlacitko nachazi v array data (bylo jiz vybrano do rezervace)
                    $(button).addClass("selected");                                             //pokud ano -> prida se mu classa "selected"
                }
                $(button).on("click", function () {                                             //pridni eventu on:click na aktualni tlacitko
                    if($(this).attr('class') != 'reserved') {                                   //kontrola zda dane tlacitko neni jiz zareervovane
                        if($(this).attr("class") == 'selected') {                               //kontrola zda jiz bylo tlacitko vybrano pokud ano ->
                            data.splice(data.indexOf(date.val() + "-" + i + "-" + j*10), 1);    //odebere se ze seznmu vybranych (array data)
                            $(this).removeClass("selected");                                    //odebere se mu classa "selected"
                        } else {                                                                //pokud vybrano nebylo ->
                            data.push(date.val() + "-" + i + "-" + j*10)                        //pridani talcitka do seznamu vybranych (array data)
                            $(this).addClass("selected");                                       //pridani classy "selected"
                        }
                    }
                })
            }
        }
    })
}