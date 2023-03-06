<style>
.v-select {
    margin-bottom: 5px;
}

.v-select.open .dropdown-toggle {
    border-bottom: 1px solid #ccc;
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

#employeeSalary label {
    font-size: 13px;
}

#employeeSalary select {
    border-radius: 3px;
}

#employeeSalary .add-button {
    padding: 2.5px;
    width: 28px;
    background-color: #298db4;
    display: block;
    text-align: center;
    color: white;
}

#employeeSalary .add-button:hover {
    background-color: #41add6;
    color: white;
}
</style>
<div id="employeeSalary">
    <form @submit.prevent="saveTransaction">
        <div class="row"
            style="margin-top: 10px;margin-bottom:15px;border-bottom: 1px solid #ccc;padding-bottom: 15px;">
            <div class="col-md-5 col-md-offset-1">
                <div class="form-group clearfix">
                    <label class="col-md-4 control-label">Transaction Type</label>
                    <div class="col-md-7">
                        <select class="form-control" v-model="transaction.transaction_type" required
                            style="padding: 0px 1px;">
                            <option value="">Select---</option>
                            <option value="Receive">Receive</option>
                            <option value="Payment">Payment</option>
                        </select>
                    </div>
                </div>
                <div class="form-group clearfix">
                    <label class="col-sm-4 control-label no-padding-right"> Transaction For </label>
                    <div class="col-sm-7">
                        <select v-model="transaction.transaction_for" class="form-control" style="padding: 0px 1px;"
                            v-on:input="onTransactionForChange">
                            <option value="" disabled>Select---</option>
                            <option value="Salary">Salary</option>
                            <option value="Advance">Advance</option>
                            <option value="Loan">Loan</option>
                        </select>
                        <!-- <select class="form-control" v-on:input="onTransactionForChange"
                            v-model="transaction.transaction_for" required style="padding: 0px 1px;">
                            <option value="">Select---</option>
                            <option value="Receive">Receive</option>
                            <option value="Payment">Payment</option>
                        </select> -->

                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4">Employee</label>
                    <div class="col-md-7">
                        <v-select v-bind:options="employees" label="display_name" v-model="selectedEmployee"
                            @input="getEmployeeDue"></v-select>
                    </div>
                    <div class="col-md-1" style="padding:0;margin-left: -15px;">
                        <a href="/employee" target="_blank" class="add-button">
                            <i class="fa fa-plus"></i>
                        </a>
                    </div>
                </div>
                <!-- <div class="form-group">
					<label class="control-label col-md-4">Salary</label>
					<div class="col-md-7">
						<input type="text" class="form-control" v-model="selectedEmployee.salary_range" disabled>
					</div>
				</div> -->

                <div class="form-group">
                    <label class="control-label col-md-4">Due Amount</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" v-model="dueAmount" disabled>
                    </div>
                </div>

            </div>
            <div class="col-md-5">
                <div class="form-group">
                    <label class="control-label col-md-4">Date</label>
                    <div class="col-md-7">
                        <input type="date" class="form-control" v-model="transaction.date">
                    </div>
                </div>
                <!-- <div class="form-group">
					<label class="control-label col-md-4">Month</label>
					<div class="col-md-7">
						<v-select v-bind:options="months" label="month_name" v-model="selectedMonth"></v-select>
					</div>
					<div class="col-md-1" style="padding:0;margin-left: -15px;">
						<a href="/month" target="_blank" class="add-button">
							<i class="fa fa-plus"></i>
						</a>
					</div>
				</div> -->
                <div class="form-group" style="display:none"
                    :style="{display:transaction.transaction_type == 'Payment' ? '' : 'none'}">
                    <label class="control-label col-md-4">Payment Amount</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" v-model="transaction.payment_amount">
                    </div>
                </div>

                <div class="form-group" style="display:none"
                    :style="{display:transaction.transaction_type == 'Receive' ? '' : 'none'}">
                    <label class="control-label col-md-4">Received Amount</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" v-model="transaction.received_amount">
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-4">Note</label>
                    <div class="col-md-7">
                        <textarea type="text" class="form-control" v-model="transaction.note"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-7 col-md-offset-4 text-right" style="padding-top: 10px">
                        <!-- <input type="button" value="Cancel" class="btn btn-danger btn-sm" @click="resetForm"> -->
                        <input type="submit" value="Save" class="btn btn-success btn-sm">
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="row">
        <div class="col-sm-12 form-inline">
            <div class="form-group">
                <label for="filter" class="sr-only">Filter</label>
                <input type="text" class="form-control" v-model="filter" placeholder="Filter">
            </div>
        </div>
        <div class="col-md-12">
            <div class="table-responsive">
                <datatable :columns="columns" :data="payments" :filter-by="filter">
                    <template scope="{ row }">
                        <tr>
                            <td>{{ row.date }}</td>
                            <td>{{ row.Employee_ID }}</td>
                            <td>{{ row.Employee_Name }}</td>
                            <!-- <td>{{ row.month_name }}</td> -->
                            <td>{{ row.transaction_type }}</td>
                            <td>{{ row.transaction_for }}</td>
                            <td>{{ row.payment_amount }}</td>
                            <td>{{ row.received_amount }}</td>
                            <!-- <td>{{ row.deduction_amount }}</td> -->
                            <td>
                                <?php if ($this->session->userdata('accountType') != 'u') { ?>
                                <button type="button" class="button edit" @click="editPayment(row)">
                                    <i class="fa fa-pencil"></i>
                                </button>
                                <button type="button" class="button" @click="deletePayment(row.emp_trans_id)">
                                    <i class="fa fa-trash"></i>
                                </button>
                                <?php } ?>
                            </td>
                        </tr>
                    </template>
                </datatable>
                <datatable-pager v-model="page" type="abbreviated" :per-page="per_page"></datatable-pager>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vuejs-datatable.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>

<script>
Vue.component('v-select', VueSelect.VueSelect);
new Vue({
    el: '#employeeSalary',
    data() {
        return {
            transaction: {
                emp_trans_id: '',
                transaction_type: 'Payment',
                transaction_for: '',
                Employee_SlNo: '',
                date: moment().format('YYYY-MM-DD'),
                // month_id: '',
                payment_amount: 0.00,
                received_amount: 0.00,
                note: '',
            },

            transactions: [],
            payments: [],
            employees: [],
            selectedEmployee: {
                Employee_SlNo: '',
                salary_range: '',
                display_name: 'Select---'
            },
            months: [],
            selectedMonth: null,
            dueAmount: 0.00,

            columns: [{
                    label: 'Date',
                    field: 'date',
                    align: 'center',
                    filterable: false
                },
                {
                    label: 'Employee Id',
                    field: 'Employee_ID',
                    align: 'center'
                },
                {
                    label: 'Employee Name',
                    field: 'Employee_Name',
                    align: 'center'
                },
                // {
                // 	label: 'Month',
                // 	field: 'month_name',
                // 	align: 'center'
                // },
                {
                    label: 'Transaction Type',
                    field: 'transaction_type',
                    align: 'center'
                },
                {
                    label: 'Transaction For',
                    field: 'transaction_for',
                    align: 'center'
                },
                {
                    label: 'Payment Amount',
                    field: 'payment_amount',
                    align: 'center'
                },
                {
                    label: 'Received Amount',
                    field: 'Received_amount',
                    align: 'center'
                },
                // { label: 'Deducted Amount', field: 'deduction_amount', align: 'center' },
                {
                    label: 'Action',
                    align: 'center',
                    filterable: false
                }
            ],
            page: 1,
            per_page: 10,
            filter: ''
        }
    },
    created() {
        this.getEmployees();
        // this.getMonths();
        this.getTransaction();
    },
    methods: {
        onTransactionForChange() {
            // if(this.payment.payment_type);
            // this.selectedMonth = '';
            // this.payable_amount = 0;
            // this.selectedEmployee = {
            //     Employee_SlNo: '',
            //     salary_range: '',
            //     display_name: 'Select---'
            // }
            this.dueAmount = 0;
        },
        getEmployees() {
            axios.get('/get_employees').then(res => {
                this.employees = res.data;
            })
        },
        // getMonths() {
        // 	axios.get('/get_months').then(res => {
        // 		this.months = res.data;
        // 	})
        // },
        getEmployeeDue() {
            if (this.selectedEmployee.Employee_SlNo == '') {
                return;
            }
            if (this.transaction.transaction_for == '') {
                alert('Transaction For');
                this.selectedEmployee = {
                    Employee_SlNo: '',
                    salary_range: '',
                    display_name: 'Select---'
                }
                return;
            }

            let data = {
                employeeId: this.selectedEmployee.Employee_SlNo,
                transacctionFor: this.transaction.transaction_for
            }

            axios.post('/get_employee_due', data).then(res => {
                if (this.transaction.emp_trans_id == '') {
                    this.dueAmount = res.data;
                } else {
                    this.dueAmount = +res.data + +this.transaction.payment_amount;

                }
            })
        },
        getTransaction() {
            axios.get('/get_employee_transaction').then(res => {
                this.payments = res.data;
            })
        },
        saveTransaction() {

            if (this.transaction.transaction_type == '') {
                alert('Select Transaction type');
                return;
            }
            if (this.transaction.transaction_for == '') {
                alert('Select Transaction Fro');
                return;
            }
            if (this.selectedEmployee.Employee_SlNo == '') {
                alert('Select employee');
                return;
            }

            // if (this.selectedMonth == null) {
            // 	alert('Select month');
            // 	return;
            // }


            this.transaction.Employee_SlNo = this.selectedEmployee.Employee_SlNo;
            // this.payment.month_id = this.selectedMonth.month_id;

            let url = '/add_employee_transaction';
            if (this.transaction.emp_trans_id != '') {
                url = '/update_employee_transaction';
            }

            // console.log(this.transaction);
            // return
            axios.post(url, this.transaction)
                .then(res => {
                    let r = res.data;
                    alert(r.message);
                    if (r.success) {
                        this.resetForm();
                        this.getTransaction();
                    }
                })
                .catch(error => alert(error.response.statusText))
        },
        editPayment(payment) {
            let keys = Object.keys(this.transaction);
            keys.forEach(key => this.transaction[key] = payment[key]);

            this.selectedEmployee = {
                Employee_SlNo: payment.Employee_SlNo,
                Employee_Name: payment.Employee_Name,
                display_name: `${payment.Employee_Name} - ${payment.Employee_ID}`,
                salary_range: payment.salary_range
            }
            // this.selectedMonth = {
            // 	month_id: payment.month_id,
            // 	month_name: payment.month_name
            // }

        },
        deletePayment(trans_id) {
            let confirmation = confirm('Are you sure?');
            if (confirmation == false) {
                return;
            }
            axios.post('/delete_employee_transaction', {
                    empTransId: trans_id
                })
                .then(res => {
                    let r = res.data;
                    alert(r.message);
                    if (r.success) {
                        this.getTransaction();
                    }
                })
        },
        resetForm() {
            this.transaction = {
                emp_trans_id: '',
                transaction_type: 'Payment',
                transaction_for: '',
                Employee_SlNo: '',
                date: moment().format('YYYY-MM-DD'),
                // month_id: '',
                payment_amount: 0.00,
                received_amount: 0.00,
                note: '',
            };

            this.dueAmount = 0

            this.selectedEmployee = {
                Employee_SlNo: '',
                salary_range: '',
                display_name: 'Select---'
            };
            // this.selectedMonth = null;
        }
    }
})
</script>