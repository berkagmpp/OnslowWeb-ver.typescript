'use strict';

(function () {
    let customers = [];

    const getCustomers = function () {
        return new Promise((resolve, reject) => {
            const xhttp = new XMLHttpRequest();

            xhttp.onreadystatechange = function () {
                if (xhttp.readyState !== XMLHttpRequest.DONE) {
                    return;
                }
                if (xhttp.status >= 200 && xhttp.status < 400) {
                    resolve(JSON.parse(xhttp.responseText));
                } else {
                    reject(new Error(xhttp.status));
                }
            }

            const name = document.getElementById("input_name").value;

            xhttp.open("GET", `/onslow-ts/api/customers.php?name=${name}`, true);
            xhttp.send();
        });
    }

    const display = function (data) {
        let tbl = document.getElementById("tblcustomers");
        let rowCount = tbl.rows.length;
        for (let i = 1; i < rowCount; i++) {
            //delete from the top - row 0 is the table header we keep
            tbl.deleteRow(1);
        }

        //populate the table
        for (let i = 0; i < data.length; i++) {
            let id = data[i]['customerID'];
            let customerName = data[i]['customer_column'];

            //concatenate our actions urls into a single string
            let urls = '<a href="viewcustomer.php?id=' + id + '">[view]</a>';
            urls += '<a href="editcustomer.php?id=' + id + '">[edit]</a>';
            urls += '<a href="deletecustomer.php?id=' + id + '">[delete]</a>';

            //create a table row with three cells  
            let tr = tbl.insertRow(-1);
            let tabCell = tr.insertCell(-1);
            tabCell.innerHTML = id; //id
            tabCell = tr.insertCell(-1);
            tabCell.innerHTML = customerName; //name      
            tabCell = tr.insertCell(-1);
            tabCell.innerHTML = urls; //action URLS            
        }
    }

    getCustomers()
        .then(data => {
            customers = data;
            display(customers);
      }).catch(console.error);

    // 1. reopen with sql query $condition in the booking.php file - use keydown and keyup
    const name = document.getElementById("input_name");

    const nameFinding = function (e) {
        const searchString = e.target.value.toLowerCase();

        if (searchString.trim() == '') {
            getCustomers()
                .then(data => {
                customers = data;
                display(customers);
              }).catch(console.error);

        } else {
            getCustomers()
                .then(data => {
                    customers = data;
                    display(customers);
              }).catch(console.error);
        }
    }

    name.addEventListener("keyup", nameFinding, false);
    name.addEventListener("keydown", nameFinding, false);

    // 1. reopen with sql query $condition in the customers.php file - keyup doesn't work for backsapce
    // document.getElementById("input_name").addEventListener('keyup', (e) => {
    //   const searchString = e.target.value.toLowerCase();

    //   if (searchString.trim() == '') {
    //     display(customers);
    //   } else {
    //     getCustomers()
    //     .then(data => {
    //       customers = data;
    //       display(customers);
    //     }).catch(console.error);
    //   }
    // });

    // 2. For of (front-end)
    // document.getElementById("input_name").addEventListener('keyup', (e) => {
    //   const searchString = e.target.value.toLowerCase();

    //   if (searchString.trim() == '') {
    //     display(customers);
    //   } else {
    //     let newCustomers = [];

    //     for (let customer of customers) {
    //       if (customer.customer_column.toLowerCase().includes(searchString)) {
    //         newCustomers.push(customer);
    //       }
    //     }
    //     display(newCustomers);
    //   }
    // });

    // 3. Filter (front-end)
    // document.getElementById("input_name").addEventListener('keyup', (e) => {
    //   const searchString = e.target.value.toLowerCase();

    //   if (searchString.trim() == '') {
    //     display(customers);
    //   } else {
    //     let newCustomers = [];

    //   // can use filter instead of for of
    //   customers = customers.filter((e) => {
    //       return (
    //         e.customer_column.toLowerCase().includes(searchString)
    //       )
    //   });
    //     display(newCustomers);
    //   }
    // });

    let msg = document.getElementById("msg");

    document.getElementById("refresh").addEventListener('click', () => {
        name.value = '';
        msg.innerHTML = "cleared";
        setTimeout(() => {
            msg.innerHTML = "";
        }, 2000)
        getCustomers()
            .then(data => {
                customers = data;
                display(customers);
          }).catch(console.error);
    })

    // front-end validation
    name.addEventListener("input", function(){
        name.value = name.value.replace(/[^0-9a-zA-Z]/g, ''); // user can enter only alphaets and numbers
    })

})();

