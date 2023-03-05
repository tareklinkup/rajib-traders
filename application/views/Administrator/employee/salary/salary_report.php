<style>
.v-select {
    margin-top: -2.5px;
    float: right;
    min-width: 180px;
    margin-left: 5px;
}

.v-select .dropdown-toggle {
    padding: 0px;
    height: 25px;
}

.v-select input[type=search],
.v-select input[type=search]:focus {
    margin: 0px;
}

.v-select .vs__selected-options {
    overflow: hidden;
    flex-wrap: nowrap;
}

.v-select .selected-tag {
    margin: 2px 0px;
    white-space: nowrap;
    position: absolute;
    left: 0px;
}

.v-select .vs__actions {
    margin-top: -5px;
}

.v-select .dropdown-menu {
    width: auto;
    overflow-y: auto;
}

#searchForm select {
    padding: 0;
    border-radius: 4px;
}

#searchForm .form-group {
    margin-right: 5px;
}

#searchForm * {
    font-size: 13px;
}

.record-table {
    width: 100%;
    border-collapse: collapse;
}

.record-table thead {
    background-color: #0097df;
    color: white;
}

.record-table th,
.record-table td {
    padding: 3px;
    border: 1px solid #454545;
}

.record-table th {
    text-align: center;
}

.custom_table th {
    padding: 5px;
}
</style>
<div id="paymentsRecord">
    <div class="row" style="border-bottom: 1px solid #ccc;padding: 3px 0;">
        <div class="col-md-12">
            <form class="form-inline" id="searchForm" @submit.prevent="getSearchResult">
                <div class="form-group">
                    <label>Search Type</label>
                    <select class="form-control" v-model="searchType" @change="onChangeSearchType">
                        <option value="">All</option>
                        <option value="employee">By Employee</option>
                    </select>
                </div>

                <div class="form-group" style="display:none;"
                    v-bind:style="{display: searchType == 'employee' && employees.length > 0 ? '' : 'none'}">
                    <label>Employee</label>
                    <v-select v-bind:options="employees" v-model="selectedEmployee" label="Employee_Name"></v-select>
                </div>

                <div class="form-group" style="display:none;"
                    v-bind:style="{display: searchType == 'month' || searchType == 'employee' ? '' : 'none'}">
                    <label>Month</label>
                    <v-select v-bind:options="months" v-model="selectedMonth" label="month_name"></v-select>
                </div>

                <div class="form-group" style="margin-top: -5px;">
                    <input type="submit" value="Search">
                </div>
            </form>
        </div>
    </div>

    <div class="row" style="margin-top:15px;display:none;" v-bind:style="{display: payments.length > 0 ? '' : 'none'}">
        <div class="col-md-12" style="margin-bottom: 10px;">
            <a href="" @click.prevent="print"><i class="fa fa-print"></i> Print</a>
        </div>
        <div class="col-md-12">
            <div class="table-responsive" id="reportContent">

                <template>

                    <table class="record-table">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Payment Date</th>
                                <th>Employee ID</th>
                                <th>Employee Name</th>
                                <th>Salary</th>
                                <th>Advance Adjust</th>
                                <th>Deduction</th>
                                <th>Payment</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="payment in payments">
                                <td>{{ payment.month_name }}</td>
                                <td>{{ payment.date }}</td>
                                <td>{{ payment.Employee_ID }}</td>
                                <td>{{ payment.Employee_Name }}</td>
                                <td style="text-align:right;">{{ payment.salary_range }}</td>
                                <td style="text-align:right;">{{ payment.advance_adjust }}</td>
                                <td style="text-align:right;">{{ payment.leave_deduction }}</td>
                                <td style="text-align:right;">{{ payment.total_amount }}</td>
                                <td>{{ payment.comment }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr style="font-weight:bold;">
                                <td colspan="4" style="text-align:right;">Total=</td>
                                <td style="text-align:right;">
                                    {{ payments.reduce((prev, curr) => { return prev + parseFloat(curr.salary_range)}, 0) | decimal }}
                                </td>
                                <td style="text-align:right;">
                                    {{ payments.reduce((prev, curr) => { return prev + parseFloat(curr.advance_adjust)}, 0) | decimal }}
                                </td>
                                <td style="text-align:right;">
                                    {{ payments.reduce((prev, curr) => { return prev + parseFloat(curr.leave_deduction)}, 0) | decimal }}
                                </td>
                                <td style="text-align:right;">
                                    {{ payments.reduce((prev, curr) => { return prev + parseFloat(curr.total_amount)}, 0) | decimal }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </template>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url();?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url();?>assets/js/moment.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/lodash.min.js"></script>

<script>
Vue.component('v-select', VueSelect.VueSelect);
new Vue({
    el: '#paymentsRecord',
    data() {
        return {
            searchType: '',
            recordType: 'without_details',
            months: [],
            selectedMonth: null,
            employees: [],
            selectedEmployee: null,
            payments: [],
            searchTypesForRecord: ['', 'month'],
            searchTypesForDetails: ['employee']
        }
    },
    filters: {
        decimal(value) {
            return value == null || value == '' ? '0.00' : parseFloat(value).toFixed(2);
        }
    },
    methods: {
        onChangeSearchType() {
            this.payments = [];
            if (this.searchType == 'month') {
                this.getMonths();
            } else if (this.searchType == 'employee') {
                this.getMonths();
                this.getEmployees();
            }
        },
        getMonths() {
            axios.get('/get_months').then(res => {
                this.months = res.data;
            })
        },
        getEmployees() {
            axios.get('/get_employees').then(res => {
                this.employees = res.data;
            })
        },
        getSearchResult() {
            if (this.searchType == '') {
                this.selectedMonth = null;
            }

            if (this.searchType != 'employee') {
                this.selectedEmployee = null;
            }

            this.getPaymentDetails();
        },
        getPaymentsRecord() {
            if (this.searchType == 'month' && this.selectedMonth == null) {
                alert('Select Month');
                return;
            }

            let filter = {
                month_id: this.selectedMonth == null ? '' : this.selectedMonth.month_id
            }

            if (this.recordType == 'with_details') {
                filter.details = true;
            }

            let url = '/get_payments';

            axios.post(url, filter)
                .then(res => {
                    this.payments = res.data;
                })
                .catch(error => {
                    if (error.response) {
                        alert(`${error.response.status}, ${error.response.statusText}`);
                    }
                })
        },
        getPaymentDetails() {
            // if (this.selectedEmployee == null) {
            //     alert('Select Employee');
            //     return;
            // }
            let filter = {
                month_id: this.selectedMonth == null ? '' : this.selectedMonth.month_id,
                employee_id: this.selectedEmployee == null ? '' : this.selectedEmployee.Employee_SlNo
            }

            axios.post('/get_salary_details', filter)
                .then(res => {
                    this.payments = res.data;
                })
                .catch(error => {
                    if (error.response) {
                        alert(`${error.response.status}, ${error.response.statusText}`);
                    }
                })
        },
        deletePayment(paymentId) {
            let deleteConf = confirm('Are you sure?');
            if (deleteConf == false) {
                return;
            }
            axios.post('/delete_payment', {
                    paymentId
                })
                .then(res => {
                    let r = res.data;
                    alert(r.message);
                    if (r.success) {
                        this.getPaymentsRecord();
                    }
                })
                .catch(error => {
                    if (error.response) {
                        alert(`${error.response.status}, ${error.response.statusText}`);
                    }
                })
        },
        async print() {


            let reportContent = `
					<div class="container">
						<div class="row">
							<div class="col-xs-12 text-center">
								<h3>Employee Payment Record</h3>
							</div>
						</div>

						<div class="row">
							<div class="col-xs-12">
								${document.querySelector('#reportContent').innerHTML}
							</div>
						</div>
					</div>
				`;

            var reportWindow = window.open('', 'PRINT', `height=${screen.height}, width=${screen.width}`);
            reportWindow.document.write(`
					<?php $this->load->view('Administrator/reports/reportHeader.php');?>
				`);

            reportWindow.document.head.innerHTML += `
					<style>
						.record-table{
							width: 100%;
							border-collapse: collapse;
						}
						.record-table thead{
							background-color: #0097df;
							color:white;
						}
						.record-table th, .record-table td{
							padding: 3px;
							border: 1px solid #454545;
						}
						.record-table th{
							text-align: center;
						}
						.custom_table th{
							padding: 5px;
						}
					</style>
				`;
            reportWindow.document.body.innerHTML += reportContent;

            if (this.searchType == '' && this.searchType == 'month' && this.recordType ==
                'without_details') {
                let rows = reportWindow.document.querySelectorAll('.record-table tr');
                rows.forEach(row => {
                    row.lastChild.remove();
                })
            }


            reportWindow.focus();
            await new Promise(resolve => setTimeout(resolve, 1000));
            reportWindow.print();
            reportWindow.close();
        }
    }
})
</script>