'use strict';

(function () {
    const getCustomers = () => {
        return new Promise<string>((resolve, reject): void => {
            const xhttp: XMLHttpRequest = new XMLHttpRequest();

            xhttp.onreadystatechange = (): void => {
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

            const name = document.getElementById("input_name") as HTMLInputElement;
            const nameValue: (string|number) = name.value;

            xhttp.open("GET", `/onslow-ts/api/customers.php?name=${nameValue}`, true);
            xhttp.send();
        });
    }

    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const display = function (data: any) {
        const tbl = document.getElementById("tblcustomers");
        if (tbl instanceof HTMLTableElement) {
            const rowCount: number = tbl.rows.length;
            for (let i = 1; i < rowCount; i++) {
                //delete from the top - row 0 is the table header we keep
                tbl.deleteRow(1);
            }
            //populate the table
            for (let i = 0; i < data.length; i++) {
                const id: number = data[i]['customerID'];
                const customerName: string = data[i]['customer_column'];

                //concatenate our actions urls into a single string
                let urls: string = '<a href="viewcustomer.php?id=' + id + '">[view]</a>';
                urls += '<a href="editcustomer.php?id=' + id + '">[edit]</a>';
                urls += '<a href="deletecustomer.php?id=' + id + '">[delete]</a>';

                //create a table row with three cells  
                const tr = tbl.insertRow(-1);
                // eslint-disable-next-line @typescript-eslint/no-explicit-any
                let tabCell: any = tr.insertCell(-1);
                tabCell.innerHTML = id; //id
                tabCell = tr.insertCell(-1);
                tabCell.innerHTML = customerName; //name      
                tabCell = tr.insertCell(-1);
                tabCell.innerHTML = urls; //action URLS            
            }
        }
    }

    getCustomers()
        .then(data => {
            display(data)
      }).catch(console.error);

    // 1. reopen with sql query $condition in the booking.php file - use keydown and keyup
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const nameFinding = (): void => {
        getCustomers()
            .then(data => {
                display(data);
            }).catch(console.error);
    }

    const name = document.getElementById("input_name") as HTMLInputElement;

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

    const msg = document.getElementById("msg") as HTMLElement;
    const refresh = document.getElementById("refresh") as HTMLElement;

    refresh.addEventListener('click', () => {
        name.value = '';
        msg.innerHTML = "cleared";
        setTimeout(() => {
            msg.innerHTML = "";
        }, 2000)
        getCustomers()
            .then(data => {
                display(data);
        }).catch(console.error);
    })

    // front-end validation
    name.addEventListener("input", function(){
        name.value = name.value.replace(/[^0-9a-zA-Z]/g, ''); // user can enter only alphaets and numbers
    })

})();

