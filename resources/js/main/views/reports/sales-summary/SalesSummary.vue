<template>
    <a-row>
        <a-col :span="24">
            <div v-if="!table.loading" class="table-responsive">
                <a-table
                    :columns="salesSummaryColumns"
                    :row-key="(record) => record.xid"
                    :data-source="table.data.sales"
                    :pagination="table.pagination"
                    :loading="table.loading"
                    @change="handleTableChange"
                    id="sales-summary-reports-table"
                    bordered
                    size="middle"
                >
                    <template #bodyCell="{ column, record }">
                        <template v-if="column.dataIndex === 'order_date'">
                            {{ formatDateTime(record.order_date) }}
                        </template>
                        <template v-if="column.dataIndex === 'payment_type'">
                            {{
                                record.payment_type == "in"
                                    ? $t("menu.payment_in")
                                    : $t("menu.payment_out")
                            }}
                        </template>
                        <template v-if="column.dataIndex === 'user_id'">
                            <UserInfo :user="record.user" />
                        </template>
                        <template v-if="column.dataIndex === 'amount'">
                            {{ formatAmountCurrency(record.total) }}
                        </template>
                        <template v-if="column.dataIndex === 'payment_status'">
                            <PaymentStatus
                                :paymentStatus="record.payment_status"
                            />
                        </template>
                        <template v-if="column.dataIndex === 'staff_user_id'">
                            {{ record.staff_member.name }}
                        </template>
                        <template v-if="column.dataIndex === 'order_status'">
                            <OrderStatus :data="record" />
                        </template>
                    </template>
                </a-table>
            </div>
        </a-col>
    </a-row>
    <a-row>
        <a-col style="display: flex; flex-wrap: wrap" :span="24">
            <a-card
                v-for="staffStat in staffStats"
                :key="staffStat.xid"
                style="width: 50%; display: flex"
            >
                <a-card-meta
                    style="margin-bottom: 10px"
                    :title="staffStat.staff_member.name"
                >
                </a-card-meta>
                <template #cover>
                    <img
                        style="width: 175px"
                        :src="staffStat.staff_member.profile_image_url"
                    />
                </template>
                <div style="line-height: 2; font-size: 16px">
                    <p>Orders: {{ staffStat.orders_count }}</p>
                    <p>
                        Total Amount:
                        {{ formatAmountCurrency(staffStat.total) }}
                    </p>
                </div>
            </a-card>
        </a-col>
    </a-row>
</template>

<script>
import { defineComponent, ref, onMounted, watch, watchEffect } from "vue";
import datatable from "../../../../common/composable/datatable";
import common from "../../../../common/composable/common";
import UserInfo from "../../../../common/components/user/UserInfo.vue";
import fields from "./fields";
import PaymentStatus from "../../../../common/components/order/PaymentStatus.vue";
import OrderStatus from "../../../../common/components/order/OrderStatus.vue";

export default defineComponent({
    props: {
        user_id: null,
        paymentMode: null,
        dates: {
            default: [],
            type: null,
        },
    },
    components: {
        UserInfo,
        PaymentStatus,
        OrderStatus,
    },
    setup(props) {
        const { salesSummaryColumns, orderHashableColumns } = fields();
        const { formatDateTime, formatAmountCurrency, selectedWarehouse } =
            common();
        const datatableVariables = datatable();

        const staffStats = ref([]);

        onMounted(() => {
            const propsData = props;
            getData(propsData);
        });

        const getData = (propsData) => {
            const filters = {};

            if (propsData.user_id && propsData.user_id != undefined) {
                filters.staff_user_id = propsData.user_id;
            }

            datatableVariables.tableUrl.value = {
                url: "sales-summary?fields=id,xid,order_date,order_status,invoice_number,total,payment_status,user_id,x_user_id,user{id,xid,name,profile_image,profile_image_url,user_type},staff_user_id,x_staff_user_id,staffMember{id,xid,name,profile_image,profile_image_url,user_type}",
                filters,
                extraFilters: {
                    dates: propsData.dates,
                },
            };
            datatableVariables.hashable.value = [...orderHashableColumns];
            datatableVariables.table.sorter = {
                field: "order_date",
                order: "desc",
            };
            datatableVariables.exportDetails.value = {
                allowExport: true,
                exportType: "sales_summary_reports",
            };

            datatableVariables.fetch({
                page: 1,
            });
        };

        watchEffect(() => {
            staffStats.value = datatableVariables.table.data.staff_stats;
            delete datatableVariables.table.data.staff_stats;
        });

        watch(props, (newVal, oldVal) => {
            getData(newVal);
        });

        watch(selectedWarehouse, (newVal, oldVal) => {
            getData(props);
        });

        return {
            salesSummaryColumns,
            staffStats,
            ...datatableVariables,

            formatDateTime,
            formatAmountCurrency,
        };
    },
});
</script>
