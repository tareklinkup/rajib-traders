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
			<form class="form-inline" @submit.prevent="SalaryGenerate">
				<div class="form-group">
					<label>Select Month</label>
					<v-select v-bind:options="months" v-model="selectedMonth" label="month_name"></v-select>
				</div>

				<div class="form-group" style="margin-top: -5px;">
					<input type="submit" class="search-button" value="Generate">
				</div>
			</form>
		</div>
	</div>

	<div class="row" style="margin-top: 10px;display:none" :style="{display:salary.length > 0 ? '' : 'none'}">
		<!-- <div class="col-md-12">
			<a href="" @click.prevent="print"><i class="fa fa-print"></i> Print</a>
		</div> -->
		<div class="col-md-12">
			<div class="table-responsive" id="reportContent">
				<!-- <div style="display:none;" v-bind:style="{display: payments.length > 0 && reportType == 'records' ? '' : 'none'}"> -->
				<!-- <h3 style="text-align:center;">Payment Records</h3> -->
				<table class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th>Sl</th>
							<th>Employee Id</th>
							<th>Employee Name</th>
							<th>Department</th>
							<th>Designation</th>
							<th>Salary</th>
							<th>Leave Deduction</th>
							<th>Advance Adjust</th>
							<!-- <th>Loan Adjust</th> -->
							<th>Total</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="(sal, index) in salary">
							<td>{{ index + 1 }}</td>
							<td>{{ sal.Employee_ID }}</td>
							<td>{{ sal.Employee_Name }}</td>
							<td>{{ sal.Department_Name }}</td>
							<td>{{ sal.Designation_Name }}</td>
							<td style="text-align:right;">{{ sal.salary_range }}</td>
							<td>
								<input style="text-align: center;width: 120px;" @input="calculateTotal(index)" type="text" v-model.number="sal.leave_deduction">
							</td>
							<td>
								<input style="text-align: center;width: 120px;" @input="calculateTotal(index)" type="text" v-model.number="sal.advance_adjust">
							</td>
							<!-- <td>
								<input style="text-align: center;width: 120px;" @input="calculateTotal(index)" type="text" v-model.number="sal.loan_adjust">
							</td> -->
							<td style="text-align:right;">{{ sal.total_amount }}</td>
						</tr>
					</tbody>
					<tfoot>
						<tr style="font-weight:bold;">
							<td colspan="5" style="text-align:right;">Total</td>
							<td style="text-align:right;">{{ salary.reduce((prev, curr) => { return prev + parseFloat(curr.salary_range)}, 0).toFixed(2) }}</td>
							<td style="text-align:center;">{{ salary.reduce((prev, curr) => { return prev + parseFloat(curr.leave_deduction)}, 0).toFixed(2) }}</td>
							<td style="text-align:center;">{{ salary.reduce((prev, curr) => { return prev + parseFloat(curr.advance_adjust)}, 0).toFixed(2) }}</td>
							<!-- <td style="text-align:center;">{{ salary.reduce((prev, curr) => { return prev + parseFloat(curr.loan_adjust)}, 0).toFixed(2) }}</td> -->
							<td style="text-align:right;">{{ salary.reduce((prev, curr) => { return prev + parseFloat(curr.total_amount)}, 0).toFixed(2) }}</td>
						</tr>
						<tr>
							<td colspan="10" style="text-align:center;padding: 10px;">
								<button @click.prevent="saveSalary" class="btn btn-success">Save Salary</button>
							</td>
						</tr>
					</tfoot>
				</table>
				<!-- </div> -->
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
				months: [],
				selectedMonth: {
					month_id: '',
					month_name: 'Select---'
				},
				salary: [],
			}
		},
		computed: {},
		created() {
			this.getMonths();
			// this.getEmployees();
		},
		methods: {
			getMonths() {
				axios.get('/get_months').then(res => {
					this.months = res.data;
				})
			},

			SalaryGenerate() {
				if (this.selectedMonth.month_id == '') {
					alert("Select a month")
					return
				}

				axios.post("/generateEmployeeSalary", {
					monthId: this.selectedMonth.month_id
				}).then(res => {
					this.salary = res.data;
				})
			},
			calculateTotal(index) {
				this.salary[index].total_amount = parseFloat(this.salary[index].salary_range) - (parseFloat(this.salary[index].leave_deduction) + parseFloat(this.salary[index].advance_adjust) + parseFloat(this.salary[index].loan_adjust))


			},
			saveSalary() {
				axios.post("/saveEmployeeSalary", {
					genSalary: this.salary
				}).then(res => {
					alert(res.data.message);
					if (res.data.status) {
						this.salary = [];
					}
				})
			},
		}
	})
</script>