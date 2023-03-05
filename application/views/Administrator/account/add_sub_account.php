<style>
    .v-select {
        margin-bottom: 5px;
    }

    .v-select.open .dropdown-toggle {
        border-bottom: 1px solid #ccc;
    }

    .v-select .dropdown-toggle {
        padding: 0px;
        height: 30px;
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

    .saveBtn {
        padding: 7px 22px;
        background-color: #00acb5 !important;
        border-radius: 2px !important;
        border: none;
    }

    .saveBtn:hover {
        padding: 7px 22px;
        background-color: #06777c !important;
        border-radius: 2px !important;
        border: none;
    }

    select.form-control {
        padding: 1px;
    }
</style>
<div id="vehicle">
    <div class="row" style="margin-top: 10px;margin-bottom:15px;border-bottom: 1px solid #ccc;padding-bottom: 15px;">
        <form class="form-horizontal" v-on:submit.prevent="saveDate">
            <div class="col-md-5 col-md-offset-3">
                <div class="form-group">
                    <label class="control-label col-md-4">Select Account</label>
                    <label class="col-md-1" style="text-align: right;">:</label>
                    <div class="col-md-7">
                        <v-select v-bind:options="accounts" v-model="selectedAccount" label="Acc_Name"></v-select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4">Sub Account Name</label>
                    <label class="col-md-1" style="text-align: right;">:</label>
                    <div class="col-md-7">
                        <input style="height: 30px;" placeholder="Sub account name" type="text" class="form-control" v-model="inputField.sub_account_name">
                    </div>
                </div>

                <div class="form-group clearfix">
                    <div class="col-md-12" style="text-align: right;">
                        <input type="submit" class="btn saveBtn" :disabled="saveProcess" value="Add">
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="row">
        <div class="col-sm-12 form-inline">
            <div class="form-group">
                <label for="filter" class="sr-only">Filter</label>
                <input type="text" class="form-control" v-model="filter" placeholder="Filter">
            </div>
        </div>
        <div class="col-md-12">
            <div class="table-responsive">
                <datatable :columns="columns" :data="subAccounts" :filter-by="filter">
                    <template scope="{ row }">
                        <tr :style="{color: row.status == 'd' ? 'red' :''}">
                            <td>{{ row.sub_acc_id }}</td>
                            <td>{{ row.Acc_Name }}</td>
                            <td>{{ row.sub_account_name }}</td>
                            <td>{{ row.status == 'a' ? 'Active' : 'Inactive' }}</td>
                            <td>
                                <?php if ($this->session->userdata('accountType') != 'u') {
                                ?>
                                    <a href="" v-on:click.prevent=" editItem(row)"><i class="fa fa-pencil"></i></a>&nbsp;
                                    <a href="" class="button" v-on:click.prevent="deleteItem(row.sub_acc_id )"><i class="fa fa-trash"></i></a>
                                <?php  }
                                ?>
                            </td>
                            <td v-else></td>
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
        el: '#vehicle',
        data() {
            return {
                inputField: {
                    sub_acc_id: '',
                    account_id: '',
                    sub_account_name: '',
                },
                accounts: [],
                selectedAccount: {
                    Acc_SlNo: '',
                    Acc_Name: 'Select---'
                },
                saveProcess: false,
                subAccounts: [],

                columns: [{
                        label: 'SL',
                        field: 'sub_acc_id',
                        align: 'center'
                    },
                    {
                        label: 'Account Name',
                        field: 'Acc_Name',
                        align: 'center'
                    },
                    {
                        label: 'Sub Account Name',
                        field: 'sub_account_name',
                        align: 'center'
                    },
                    {
                        label: 'Status',
                        field: 'status',
                        align: 'center'
                    },
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
            this.getAccounts();
            this.getSubAccounts();
        },
        methods: {
            getAccounts() {
                axios.post('/get_accounts').then(res => {
                    this.accounts = res.data;
                })
            },
            getSubAccounts() {
                axios.post('/get_sub_account').then(res => {
                    this.subAccounts = res.data;
                })
            },
            saveDate() {
                if (this.selectedAccount.Acc_SlNo == '') {
                    alert('Select a Account!');
                    return;
                }
                if (this.inputField.sub_account_name == '') {
                    alert('Sub Account Name field is empty!');
                    return;
                }

                this.inputField.account_id = this.selectedAccount.Acc_SlNo;
                let url = '/save_sub_account';

                this.saveProcess = true;

                axios.post(url, this.inputField).then(res => {
                    let r = res.data;
                    alert(r.message);
                    if (r.success) {
                        this.saveProcess = false;
                        // this.getClientCode();
                        this.getSubAccounts();
                        this.clearForm();
                    } else {
                        this.saveProcess = false;
                    }
                })
            },
            editItem(data) {
                this.inputField.sub_acc_id = data.sub_acc_id;
                this.inputField.account_id = data.account_id;
                this.inputField.sub_account_name = data.sub_account_name;

                this.selectedAccount = {
                    Acc_SlNo: data.account_id,
                    Acc_Name: data.Acc_Name
                }
            },
            deleteItem(id) {
                let deleteConfirm = confirm('Are Your Sure to delete the item?');
                if (deleteConfirm == false) {
                    return;
                }
                axios.post('/delete_sub_account', {
                    sub_acc_id: id
                }).then(res => {
                    let r = res.data;
                    alert(r.message);
                    if (r.success) {
                        this.getSubAccounts();
                    }
                })
            },
            clearForm() {
                this.inputField.sub_acc_id = '';
                this.inputField.account_id = '';
                this.inputField.sub_account_name = '';

                this.selectedAccount = {
                    Acc_SlNo: '',
                    Acc_Name: 'Select---'
                }
            }
        }
    })
</script>