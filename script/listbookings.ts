'use strict';

(function () {
    let checkin = document.getElementById('input_checkin') as HTMLInputElement;
    let checkout = document.getElementById('input_checkout') as HTMLInputElement;
    let name = document.getElementById('input_name') as HTMLInputElement;
    let room = document.getElementById('input_room') as HTMLInputElement;

    const getBookings = function () {
        return new Promise((resolve, reject): void => {
            const xhttp: XMLHttpRequest = new XMLHttpRequest();

            xhttp.onreadystatechange = function () {
                if (xhttp.readyState !== XMLHttpRequest.DONE) {
                    return;
                }
                if (xhttp.status >= 200 && xhttp.status < 400) {
                    resolve(JSON.parse(xhttp.responseText));
                } else {
                    reject({
                        error: xhttp.status
                    });
                }
            }

            const nameVal: (string|number) = name.value;
            const roomVal: (string|number) = room.value;
            const checkinVal: string = checkin.value.toString();
            const checkoutVal: string  = checkout.value.toString();

            xhttp.open("GET", `/onslow-ts/api/bookings.php?name=${nameVal}&room=${roomVal}&checkin=${checkinVal}&checkout=${checkoutVal}`, true);
            xhttp.send();
        });
    }

    const display = function (data: any) {
        const tbl = document.getElementById('tblbookings') as HTMLTableElement;
        const rowCount = tbl.rows.length;
        for (let i = 1; i < rowCount; i++) {
            //delete from the top - row 0 is the table header we keep
            tbl.deleteRow(1);
        }

        //populate the table
        //mbrs.length is the size of our array
        for (let i = 0; i < data.length; i++) {
            const id: number = data[i]['bookingID'];
            const room: (string|number) = data[i]['room_column'];
            const customerName: (string|number)= data[i]['customer_column'];
            const checkinDate: string = data[i]['checkindate'];
            const checkoutDate: string = data[i]['checkoutdate'];
            const breakfast: string = data[i]['breakfast'];

            //concatenate our actions urls into a single string
            let urls: string = '<a href="viewbookings.php?id=' + id + '">[view]</a>';
            urls += '<a href="editbookings.php?id=' + id + '">[edit]</a>';
            urls += '<a href="deletebookings.php?id=' + id + '">[delete]</a>';

            //create a table row
            const tr = tbl.insertRow(-1);
            // eslint-disable-next-line @typescript-eslint/no-explicit-any
            let tabCell: any = tr.insertCell(-1);
            tabCell.innerHTML = id;
            tabCell = tr.insertCell(-1);
            tabCell.innerHTML = room;
            tabCell = tr.insertCell(-1);
            tabCell.innerHTML = customerName;
            tabCell = tr.insertCell(-1);
            tabCell.innerHTML = checkinDate;
            tabCell = tr.insertCell(-1);
            tabCell.innerHTML = checkoutDate;
            tabCell = tr.insertCell(-1);
            tabCell.innerHTML = breakfast;
            tabCell = tr.insertCell(-1);
            tabCell.innerHTML = urls; //action URLS            
        }
    }

    getBookings()
        .then(data => {
            display(data);
      }).catch(console.error);

    // 1. reopen with sql query $condition in the booking.php file - use keydown and keyup
    name.addEventListener('keydown', nameFinding, false);
    name.addEventListener('keyup', nameFinding, false);

    function nameFinding(): void {
        getBookings()
            .then(data => {
                display(data);
            }).catch(console.error);
    }

    // 1. reopen with sql query $condition in the booking.php file - keyup doesn't work for backsapce
    // name.addEventListener('keyup', (e) => {
    //     const nameString = e.target.value.trim();
    
    //     if (nameString == '') {
    //         display(bookings);
    //     } else {
    //         getBookings()
    //         .then(data => {
    //         bookings = data;
    //         display(bookings);
    //         }).catch(console.error);
    //     }
    // });

    // 2. For of (front-end)
    // name.addEventListener('keyup', (e) => {
    //     const searchString = e.target.value.toLowerCase();

    //     if (searchString.trim() == '') {
    //         display(bookings);
    //     } else {
    //         let newBookings = [];

    //         for (let booking of bookings) {
    //             if (booking.customer_column.toLowerCase().includes(searchString)) {
    //                 newBookings.push(booking);
    //             }
    //         }

    //         display(newBookings);
    //     }
    // });


    // 3. Filter (front-end)
    // name.addEventListener('keyup', (e) => {
    //     const searchString = e.target.value.toLowerCase();

    //     if (searchString.trim() == '') {
    //         display(bookings);
    //     } else {
    //         let newBookings = [];

    //         bookings = bookings.filter((e) => {
    //             return (
    //             e.customer_column.toLowerCase().includes(searchString)
    //             )
    //         });

    //         display(newBookings);
    //     }
    // });

    room.addEventListener('keydown', roomFinding, false);
    room.addEventListener('keyup', roomFinding, false);

    function roomFinding(): void {
        getBookings()
            .then(data => {
                display(data);
            }).catch(console.error);
    }


    
    // jQuery UI datapicker (Date Range) //
    const dateToday = new Date();

    const listdates = $('#input_checkin, #input_checkout').datepicker({
        dateFormat: 'yy-mm-dd', // set format yy-mm-dd
        defaultDate: 0, // set default date: today for user convenience
        changeMonth: true,
        numberOfMonths: 2,
        minDate: dateToday, // set user cannot select the date before today
        onSelect: function (selectedDate: any) { // set user cannot select enddate before startdate
            let option = selectedDate.id == 'input_checkin' ? 'minDate' : 'maxDate',
                instance = $(this).data('datepicker'),
                date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
            listdates.not(selectedDate).datepicker('option', option, date);
        }
    });

    // test for null error of the datepicker
    function getDate(element: any) {
        let date;
        try {
            date = $.datepicker.parseDate(dateFormat, element.value);
        } catch (error) {
            date = null;
        }
        return date;
    }

    let msg = document.getElementById('msg') as HTMLElement;

    const dateSearch = document.getElementById('date_search') as HTMLElement;
    dateSearch.addEventListener('click', () => {
        if (checkin.value == '' || checkout.value == '') {
            msg.innerHTML = 'Please fill all check-in and check-out dates';
            checkin.value = '';
            checkout.value = '';
            setTimeout(()=>{
                msg.innerHTML = '';
            },2000)
            getBookings()
                .then(data => {
                    display(data);
              }).catch(console.error);
        } else {
            getBookings()
                .then(data => {
                    display(data);
              }).catch(console.error);
        }
    })

    const refresh = document.getElementById('refresh') as HTMLElement;
    refresh.addEventListener('click', () => {
        name.value = '';
        room.value = '';
        checkin.value = '';
        checkout.value = '';
        msg.innerHTML = 'cleared';
        setTimeout(()=>{
            msg.innerHTML = '';
        },2000)
        getBookings()
            .then(data => {
                display(data);
          }).catch(console.error);
    })

    // front-end validation
    name.addEventListener('input', () => {
        name.value = name.value.replace(/[^0-9a-zA-Z]/g, ''); // user can enter only alphaets and numbers
    })

    room.addEventListener('input', () => {
        room.value = room.value.replace(/[^0-9a-zA-Z]/g, ''); // user can enter only alphaets and numbers
    })

    checkin.addEventListener('input', () => {
        checkin.value = checkin.value
            .replace(/[0-9a-zA-Z\s.,'!@#$"%^&*)(}{[\]=-]+$/, '') // user cannot enter manually
    })

    checkout.addEventListener('input', () => {
        checkout.value = checkout.value
            .replace(/[0-9a-zA-Z\s.,'!@#$"%^&*)(}{[\]=-]+$/, '') // user cannot enter manually
    })

})();

