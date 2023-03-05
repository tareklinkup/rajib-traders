<style>
	.v-select {
		margin-bottom: 5px;
		float: right;
		min-width: 200px;
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

	#salaryReport label {
		font-size: 13px;
		margin-top: 3px;
	}

	#salaryReport select {
		border-radius: 3px;
		padding: 0px;
		font-size: 13px;
	}

	#salaryReport .form-group {
		margin-right: 10px;
	}
</style>

<div id="salaryReport">
	<div class="row" style="border-bottom:1px solid #ccc;padding: 10px 0;">
		<div class="col-md-12">
			<form class="form-inline" @submit.prevent="getEmployeeTransactions">
				<!-- <div class="form-group">
					<label>Trans. Type</label>
					<select class="form-control" v-model="filter.transaction_type" style="padding: 0px 1px;width:100px">
						<option value="">Select---</option>
						<option value="Receive">Receive</option>
						<option value="Payment">Payment</option>
					</select>
				</div> -->
				<div class="form-group">
					<label>Employee</label>
					<v-select v-bind:options="comEmployees" v-model="selectedEmployee" label="display_text"></v-select>
				</div>
				<div class="form-group">
					<label> Trans. For </label>
					<select v-model="filter.transaction_for" class="form-control" @input="onChangeTrnsactionFor" style="padding: 0px 1px;width:150px">
						<option value="" disabled>Select---</option>
						<option value="Salary">Salary</option>
						<option value="Advance">Advance</option>
						<option value="Loan">Loan</option>
					</select>
				</div>

				<div class="form-group">
					<input type="date" class="form-control" v-model="filter.dateFrom">
				</div>

				<div class="form-group">
					<input type="date" class="form-control" v-model="filter.dateTo">
				</div>

				<div class="form-group" style="margin-top: -5px;">
					<input type="submit" class="search-button" value="Search">
				</div>
			</form>
		</div>
	</div>

	<div class="row" style="margin-top: 10px;display:none;" v-bind:style="{display: transactions.length > 0 ? '' : 'none'}">
		<div class="col-md-12">
			<a href="" @click.prevent="print"><i class="fa fa-print"></i> Print</a>
		</div>
		<div class="col-md-12">
			<div class="table-responsive" id="reportContent">

				<div style="display:none;" v-bind:style="{display: transactions.length > 0 ? '' : 'none'}">
					<h3 v-if="filter.transaction_for == 'Salary'" style="text-align:center;">Employee Salary Records</h3>
					<h3 v-if="filter.transaction_for == 'Advance'" style="text-align:center;">Employee Advance Records</h3>
					<h3 v-if="filter.transaction_for == 'Loan'" style="text-align:center;">Employee Loan Records</h3>
					<table class="table table-bordered table-condensed">
						<thead>
							<tr>
								<th>Sl</th>
								<th>Date</th>
								<th>Employee Name</th>
								<th>Description</th>
								<th v-if="filter.transaction_for == 'Salary'">Salary</th>
								<th v-if="filter.transaction_for == 'Salary'">Leave Deduction</th>
								<th v-if="filter.transaction_for != 'Loan'">Advance Adjust</th>
								<th>Received</th>
								<th>Paid</th>
								<th v-if="filter.transaction_for != 'Advance'">Balance</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="(payment, sl) in transactions">
								<td>{{ sl + 1 }}</td>
								<td>{{ payment.date }}</td>
								<td style="text-align: left;">{{ payment.Employee_Name }}</td>
								<td style="text-align: left;">{{ payment.description }}</td>
								<td v-if="filter.transaction_for == 'Salary'" style="text-align:right;">{{ payment.salary }}</td>
								<td v-if="filter.transaction_for == 'Salary'" style="text-align:right;">{{ payment.Leave_deduction }}</td>
								<td v-if="filter.transaction_for != 'Loan'" style="text-align:right;">{{ payment.advance_adjust }}</td>
								<td style="text-align:right;">{{ payment.paid }}</td>
								<td style="text-align:right;">{{ payment.received }}</td>
								<td v-if="filter.transaction_for != 'Advance'" style="text-align:right;">{{ payment.balance }}</td>
							</tr>
						</tbody>
						<tfoot v-if="filter.transaction_for == 'Advance'">
							<tr style="font-weight:bold;">
								<!-- <td style="text-align:right;">{{ payments.reduce((prev, curr) => { return prev + parseFloat(curr.Salary)}, 0).toFixed(2) }}</td> -->
								<td colspan="4" style="text-align:right;">Total</td>
								<td style="text-align:right;">{{ transactions.reduce((prev, curr) => { return prev + parseFloat(curr.advance_adjust)}, 0).toFixed(2) }}</td>
								<td style="text-align:right;">{{ transactions.reduce((prev, curr) => { return prev + parseFloat(curr.paid)}, 0).toFixed(2) }}</td>
								<td style="text-align:right;">{{ transactions.reduce((prev, curr) => { return prev + parseFloat(curr.received)}, 0).toFixed(2) }}</td>
							</tr>
						</tfoot>
					</table>
				</div>


			</div>
		</div>
	</div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>

<script>
	Vue.component('v-select', VueSelect.VueSelect);
	new Vue({
		el: '#salaryReport',
		data() {
			return {
				filter: {
					// transaction_type: '',
					transaction_for: '',
					dateFrom: moment().format('YYYY-MM-DD'),
					dateTo: moment().format('YYYY-MM-DD'),
					employeeId: ''
				},
				employees: [],
				selectedEmployee: {
					Employee_SlNo: '',
					display_text: 'Select---'
				},
				// months: [],
				// selectedMonth: null,
				transactions: [],
				// paymentSummary: [],
				// reportType: 'records'
			}
		},
		computed: {
			comEmployees() {
				return this.employees.map(employee => {
					employee.display_text = employee.Employee_SlNo == '' ? employee.Employee_Name : `${employee.Employee_Name} - ${employee.Employee_ID}`;
					return employee;
				})
			}
		},
		created() {
			this.getEmployees();
			// this.getMonths();
		},
		methods: {
			onChangeTrnsactionFor() {
				this.transactions = [];
			},
			getEmployees() {
				axios.get('/get_employees').then(res => {
					this.employees = res.data;
				})
			},
			getEmployeeTransactions() {
				if (this.selectedEmployee.Employee_SlNo == '') {
					alert('Select a Employee');
					return;
				}
				if (this.filter.transaction_for == '') {
					alert('Select a Transaction For');
					return;
				}

				this.filter.employeeId = this.selectedEmployee.Employee_SlNo

				// console.log(this.filter);
				// return
				axios.post('/get_employee_transaction_ledger', this.filter)
					.then(res => {
						this.transactions = res.data;
					})
			},

			async print() {
				let reportContent = `
					<div class="container">
						<div class="row">
							<div class="col-xs-12">
								${document.querySelector('#reportContent').innerHTML}
							</div>
						</div>
					</div>
				`;

				var reportWindow = window.open('', 'PRINT', `height=${screen.height}, width=${screen.width}, left=0, top=0`);
				reportWindow.document.write(`
					<?php $this->load->view('Administrator/reports/reportHeader.php'); ?>
				`);

				reportWindow.document.body.innerHTML += reportContent;

				reportWindow.focus();
				await new Promise(resolve => setTimeout(resolve, 1000));
				reportWindow.print();
				reportWindow.close();
			}
		}
	})
</script>