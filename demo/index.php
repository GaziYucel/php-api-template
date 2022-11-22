<!DOCTYPE html>
<html lang="">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Really Easy PHP API</title>
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico"/>
    <script type="text/javascript" src="assets/js/vue.global.prod.js"></script>
    <link rel="stylesheet" type="text/css" href="assets/css/style.css"/>
</head>
<body>
<div id="app">
    <h1>Demo for endpoint Data</h1>

    <p>
        <a href="index.php">Start over</a>
    </p>

    <p>
        <a href="../api/v1/" target="_blank">Api main entry point</a>
    </p>

    <h4>GET / POST / PUT / DELETE Methods</h4>

    <table>

        <tr v-for="(row, index) in rows">
            <td style="width: 20px;">
                {{ row.id }}
            </td>
            <td style="width: 100px;">
                {{ row.name }}
            </td>
            <td>
                <input type="text" name="id" id="id" value="" v-model="row.id"/>
            </td>
            <td>
                <input type="text" name="name" id="name" value="" v-model="row.name" required/>
            </td>

            <td>
                <a href="#" v-on:click="saveRow(index)">save</a> &nbsp;
            </td>
            <td>
                <a href="#" v-on:click="deleteRow(index)">delete</a>
            </td>
        </tr>
        <tr>
            <td style="width: 20px;">
                {{ newRow.id }}
            </td>
            <td style="width: 100px;">
                {{ newRow.name }}
            </td>
            <td>

            </td>
            <td>
                <input type="text" name="name" id="name" value="" v-model="newRow.name" required/>
            </td>

            <td>
                <a href="#" v-on:click="addRow()">add</a>
            </td>
            <td>

            </td>
        </tr>

    </table>

</div>

<script>
    Vue.createApp({
        data() {
            return {
                apiUrl: '../api/v1/?path=demo',
                rows: [],
                schema: [],
                newRow: [],
                AuthenticationToken: '1234567890'
            }
        },
        methods: {
            getRows() {
                fetch(this.apiUrl, {
                    method: 'GET',
                    headers: {
                        'AuthenticationToken': this.AuthenticationToken
                    }
                })
                    .then((response) => response.json())
                    .then((data) => this.rows = data);
            },
            deleteRow(index) {
                if (confirm('Are you sure you want to delete "' + this.rows[index].name + '"?')) {
                    fetch(this.apiUrl + '/' + this.rows[index].id, {
                        method: 'DELETE',
                        headers: {
                            'AuthenticationToken': this.AuthenticationToken
                        }
                    });
                    this.getRows();
                }
            },
            saveRow(index) {
                if((this.rows[index].name).length > 0){
                    if (confirm('Are you sure you want to save "' + this.rows[index].name + '"?')) {
                        fetch(this.apiUrl, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                                'AuthenticationToken': this.AuthenticationToken
                            },
                            body: new URLSearchParams({"id": this.rows[index].id, "name": this.rows[index].name})
                        });
                        this.getRows();
                    }
                }
                else {
                    alert('Name is required');
                }
            },
            addRow() {
                if((this.newRow.name).length > 0){
                    if (confirm('Are you sure you want to add "' + this.newRow.name + '"?')) {
                        fetch(this.apiUrl, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                                'AuthenticationToken': this.AuthenticationToken
                            },
                            body: new URLSearchParams({"id": "", "name": this.newRow.name})
                        });
                        this.getRows();
                        this.resetNewRow();
                    }
                }
                else {
                    alert('Name is required');
                }
            },
            resetNewRow() {
                Object.values(this.schema).forEach(key => {
                    this.newRow[key] = '';
                });
            }
        },
        mounted() {
            fetch(this.apiUrl, {
                method: 'GET',
                headers: {
                    'AuthenticationToken': this.AuthenticationToken
                }
            })
                .then((response) => response.json())
                .then((data) => this.rows = data);

            fetch(this.apiUrl + '/getSchema', {
                method: 'GET',
                headers: {
                    'AuthenticationToken': this.AuthenticationToken
                }
            })
                .then((response) => response.json())
                .then((data) => {
                    this.schema = data;
                    this.resetNewRow();
                });
        }
    }).mount('#app');
</script>

</body>
</html>
