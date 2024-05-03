<template>
    <a-modal
        :open="visible"
        :centered="true"
        :maskClosable="false"
        :title="$t('common.print_invoice')"
        width="400px"
        @cancel="onClose"
    >
        <div id="pos-invoice">
            <div
                style="max-width: 400px; margin: 0px auto"
                v-if="order && order.xid"
            >
                <div class="invoice-header">
                    <img
                        class="invoice-logo"
                        :src="selectedWarehouse.logo_url"
                        :alt="selectedWarehouse.name"
                    />
                </div>
                <div class="company-details" style="text-align: center; line-height: 1;">
                    <h2>{{ selectedWarehouse.name }}</h2>
                    <p class="company-address">
                        {{ selectedWarehouse.address }}
                    </p>
                    <h4 style="margin-bottom: 0px">
                        {{ $t("common.phone") }}: {{ selectedWarehouse.phone }}
                    </h4>
                    <h4>
                        {{ $t("common.email") }}: {{ selectedWarehouse.email }}
                    </h4>
                </div>
                <div class="tax-invoice-details" style="text-align: center">
                    <h3 class="tax-invoice-title">
                        {{
                            order.order_type
                                ? $t(
                                      ("menu." + order.order_type).replace(
                                          /-/g,
                                          "_"
                                      )
                                  )
                                : $t("sales.tax_invoice")
                        }}
                    </h3>
                    <div
                        class="invoice-customer-details"
                        style="
                            text-align: center;
                            display: flex;
                            flex-wrap: wrap;
                            line-height: 0.1;
                        "
                    >
                        <p
                            style="
                                width: 23%;
                                font-weight: bold !important;
                                font-size: 12px !important;
                            "
                            class="item-title"
                        >
                            {{ $t("sales.invoice") }} :
                        </p>
                        <p
                            style="
                                width: 27%;
                                font-size: 12px !important;
                                font-weight: 500 !important;
                            "
                            class="item-value"
                        >
                            {{ order.invoice_number }}
                        </p>
                        <p
                            style="
                                width: 23%;
                                font-weight: bold !important;
                                font-size: 12px !important;
                            "
                            class="item-title"
                        >
                            {{ $t("common.date") }} :
                        </p>
                        <p
                            style="
                                width: 27%;
                                font-size: 12px !important;
                                font-weight: 500 !important;
                            "
                            class="item-value"
                        >
                            {{ formatDate(order.order_date) }}
                        </p>
                        <p
                            style="
                                width: 23%;
                                font-weight: bold !important;
                                font-size: 12px !important;
                            "
                            class="item-title"
                        >
                            {{ $t("stock.customer") }} :
                        </p>
                        <p
                            style="
                                width: 27%;
                                font-size: 11px !important;
                                font-weight: 500 !important;
                            "
                            class="item-value"
                        >
                            {{ order.user?.name }}
                        </p>
                        <p
                            style="
                                width: 23%;
                                font-weight: bold !important;
                                font-size: 12px !important;
                            "
                            class="item-title"
                        >
                            {{ $t("common.phone") }} :
                        </p>
                        <p
                            style="
                                width: 27%;
                                font-size: 12px !important;
                                font-weight: 500 !important;
                            "
                            class="item-value"
                        >
                            {{ order.user?.phone ?? "-" }}
                        </p>
                        <p
                            style="
                                width: 30%;
                                font-weight: bold !important;
                                font-size: 12px !important;
                            "
                            class="item-title"
                        >
                            {{ $t("common.address") }} :
                        </p>
                        <p
                            style="
                                width: fit-content;
                                max-width: 70%;
                                min-width: 50%;
                                font-size: 12px !important;
                                font-weight: 500 !important;
                            "
                            class="item-value"
                        >
                            {{ order.user?.address ?? "-" }}
                        </p>
                        <p
                            style="
                                width: 23%;
                                font-weight: bold !important;
                                font-size: 12px !important;
                            "
                            class="item-title"
                        >
                            {{ $t("common.email") }} :
                        </p>
                        <p
                            style="
                                width: 29%;
                                font-size: 12px !important;
                                font-weight: 500 !important;
                            "
                            class="item-value"
                        >
                            {{ order.user?.email }}
                        </p>
                        <p
                            style="
                                width: 23%;
                                font-weight: bold !important;
                                font-size: 12px !important;
                            "
                            class="item-title"
                        >
                            {{ $t("stock.sold_by") }} :
                        </p>
                        <p
                            style="
                                width: 25%;
                                font-size: 12px !important;
                                font-weight: 500 !important;
                            "
                            class="item-value"
                        >
                            {{ order.staff_member.name }}
                        </p>
                    </div>
                </div>
                <div class="tax-invoice-items">
                    <table style="width: 100%; border-collapse: collapse">
                        <thead style="background: #eee; text-align: center">
                            <td style="width: 5%">#</td>
                            <td style="width: 25%">{{ $t("common.item") }}</td>
                            <td
                                :style="{
                                    width: selectedWarehouse.show_mrp_on_invoice
                                        ? '10%'
                                        : '25%',
                                }"
                            >
                                {{ $t("common.qty") }}
                            </td>
                            <td
                                v-if="selectedWarehouse.show_mrp_on_invoice"
                                :style="{
                                    width: selectedWarehouse.show_mrp_on_invoice
                                        ? '20%'
                                        : '20%',
                                }"
                            >
                                {{ $t("product.mrp") }}
                            </td>
                            <td
                                :style="{
                                    width: selectedWarehouse.show_mrp_on_invoice
                                        ? '20%'
                                        : '25%',
                                }"
                            >
                                {{ $t("common.rate") }}
                            </td>
                            <td
                                :style="{
                                    width: selectedWarehouse.show_mrp_on_invoice
                                        ? '20%'
                                        : '25%',
                                    textAlign: 'right',
                                }"
                            >
                                {{ $t("common.total") }}
                            </td>
                        </thead>
                        <tbody style="text-align: center">
                            <tr
                                class="item-row"
                                v-for="(item, index) in order.items"
                                :key="item.xid"
                            >
                                <td
                                    style="
                                        border: 2px solid #b4b4b4 !important;
                                        font-size: 13px !important;
                                    "
                                >
                                    {{ index + 1 }}
                                </td>
                                <td
                                    style="
                                        border: 2px solid #b4b4b4 !important;
                                        font-size: 13px !important;
                                    "
                                >
                                    {{ item.product.name }}
                                </td>
                                <td
                                    style="
                                        border: 2px solid #b4b4b4 !important;
                                        font-size: 13px !important;
                                    "
                                >
                                    {{
                                        !item.unit
                                            ? item.quantity
                                            : item.quantity +
                                              " " +
                                              item.unit?.short_name
                                    }}
                                </td>
                                <td
                                    style="
                                        border: 2px solid #b4b4b4 !important;
                                        font-size: 13px !important;
                                    "
                                    v-if="selectedWarehouse.show_mrp_on_invoice"
                                >
                                    {{
                                        item.mrp
                                            ? formatAmountCurrency(item.mrp)
                                            : "-"
                                    }}
                                </td>
                                <td
                                    style="
                                        border: 2px solid #b4b4b4 !important;
                                        font-size: 13px !important;
                                    "
                                >
                                    {{ formatAmountCurrency(item.unit_price) }}
                                </td>
                                <td
                                    style="
                                        border: 2px solid #b4b4b4 !important;
                                        font-size: 13px !important;
                                        text-align: right;
                                    "
                                >
                                    {{ formatAmountCurrency(item.subtotal) }}
                                </td>
                            </tr>
                            <tr class="item-row-other">
                                <td
                                    :colspan="
                                        selectedWarehouse.show_mrp_on_invoice
                                            ? 4
                                            : 3
                                    "
                                    style="text-align: right"
                                >
                                    {{ $t("stock.order_tax") }}
                                </td>
                                <td colspan="2" style="text-align: right">
                                    {{ formatAmountCurrency(order.tax_amount) }}
                                </td>
                            </tr>
                            <tr class="item-row-other">
                                <td
                                    :colspan="
                                        selectedWarehouse.show_mrp_on_invoice
                                            ? 4
                                            : 3
                                    "
                                    style="text-align: right"
                                >
                                    {{ $t("stock.discount") }}
                                </td>
                                <td colspan="2" style="text-align: right">
                                    {{ formatAmountCurrency(order.discount) }}
                                </td>
                            </tr>
                            <tr class="item-row-other">
                                <td
                                    :colspan="
                                        selectedWarehouse.show_mrp_on_invoice
                                            ? 4
                                            : 3
                                    "
                                    style="text-align: right"
                                >
                                    {{ $t("stock.shipping") }}
                                </td>
                                <td colspan="2" style="text-align: right">
                                    {{ formatAmountCurrency(order.shipping) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="tax-invoice-totals">
                    <table style="width: 100%">
                        <tr>
                            <td style="width: 30%">
                                <h3 style="margin-bottom: 0px">
                                    {{ $t("common.items") }}:
                                    {{ order.total_items }}
                                </h3>
                            </td>
                            <td style="width: 30%">
                                <h3 style="margin-bottom: 0px">
                                    {{ $t("common.qty") }}:
                                    {{ order.total_quantity }}
                                </h3>
                            </td>
                            <td style="width: 40%; text-align: center">
                                <h3 style="margin-bottom: 0px">
                                    {{ $t("common.total") }}:
                                    {{ formatAmountCurrency(order.total) }}
                                </h3>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="paid-amount-deatils">
                    <table style="width: 100%">
                        <thead style="background: #eee">
                            <td style="width: 50%">
                                {{ $t("payments.paid_amount") }}
                            </td>
                            <td style="width: 50%">
                                {{ $t("payments.due_amount") }}
                            </td>
                        </thead>
                        <tbody>
                            <tr class="paid-amount-row">
                                <td>
                                    {{
                                        formatAmountCurrency(order.paid_amount)
                                    }}
                                </td>
                                <td>
                                    {{ formatAmountCurrency(order.due_amount) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div>
                    <table style="width: 100%">
                        <tr style="text-align: center">
                            <td style="width: 100%">
                                <h4
                                    style="margin-bottom: 0px"
                                    v-if="order.order_payments"
                                >
                                    {{ $t("invoice.payment_mode") }}:
                                    <span
                                        v-for="currentOrderPayments in order.order_payments"
                                        :key="currentOrderPayments.xid"
                                        style="margin-right: 5px"
                                    >
                                        {{
                                            formatAmountCurrency(
                                                currentOrderPayments.amount
                                            )
                                        }}
                                        (<span
                                            v-if="
                                                currentOrderPayments.payment &&
                                                currentOrderPayments.payment
                                                    .payment_mode &&
                                                currentOrderPayments.payment
                                                    .payment_mode.name
                                            "
                                        >
                                            {{
                                                currentOrderPayments.payment
                                                    .payment_mode.name
                                            }}
                                        </span>
                                        )
                                    </span>
                                </h4>
                                <h3 style="margin-bottom: 0px" v-else>
                                    {{ $t("invoice.payment_mode") }}: -
                                </h3>
                            </td>
                        </tr>
                    </table>
                </div>
                <div
                    v-if="selectedWarehouse.show_discount_tax_on_invoice"
                    class="discount-details"
                >
                    <table>
                        <tr>
                            <td>
                                <p>
                                    {{ $t("invoice.total_discount_on_mrp") }} :
                                </p>
                            </td>
                            <td>
                                <p>
                                    {{
                                        order.saving_on_mrp
                                            ? formatAmountCurrency(
                                                  order.saving_on_mrp
                                              )
                                            : formatAmountCurrency(0)
                                    }}
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p>{{ $t("invoice.total_discount") }} :</p>
                            </td>
                            <td>
                                <p>{{ order.saving_percentage }}%</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p>{{ $t("invoice.total_tax") }} :</p>
                            </td>
                            <td>
                                <p>
                                    {{
                                        order.total_tax_on_items
                                            ? formatAmountCurrency(
                                                  order.total_tax_on_items
                                              )
                                            : formatAmountCurrency(0)
                                    }}
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>
                <div
                    class="barcode-details"
                    style="text-align: center; direction: ltr"
                >
                    <BarcodeGenerator
                        :value="order.invoice_number.split('-')[1]"
                        :text="order.invoice_number + ''"
                        :height="
                            order.invoice_number.split('-')[1].length > 6
                                ? 25
                                : 50
                        "
                        :width="
                            order.invoice_number.split('-')[1].length > 6
                                ? 1
                                : 3
                        "
                        :fontSize="15"
                        :elementTag="'svg'"
                    />
                </div>
                <div class="thanks-details" style="text-align: center">
                    <h3>{{ $t("invoice.thanks_message") }}</h3>
                    <h4>Powered by linkawyx.com</h4>
                </div>
            </div>
        </div>

        <template #footer>
            <div class="footer-button">
                <a-button type="primary" @click="printInvoice">
                    <template #icon>
                        <PrinterOutlined />
                    </template>
                    {{ $t("common.print_invoice") }}
                </a-button>
            </div>
        </template>
    </a-modal>
</template>

<script>
import { ref, onMounted, onUnmounted, defineComponent } from "vue";
import { PrinterOutlined } from "@ant-design/icons-vue";
import common from "../../../../common/composable/common";
import BarcodeGenerator from "../../../../common/components/barcode/BarcodeGenerator.vue";
import QRcodeGenerator from "../../../../common/components/barcode/QRcodeGenerator.vue";
const posInvoiceCssUrl = window.config.pos_invoice_css;

export default defineComponent({
    props: ["visible", "order"],
    emits: ["closed", "success"],
    components: {
        PrinterOutlined,
        BarcodeGenerator,
        QRcodeGenerator,
    },
    setup(props, { emit }) {
        const { formatAmountCurrency, formatDate, selectedWarehouse } =
            common();

        const onClose = () => {
            emit("closed");
        };

        const printInvoice = () => {
            var invoiceContent =
                document.getElementById("pos-invoice").innerHTML;
            var newWindow = window.open("", "", "height=500, width=500");
            newWindow.document.write(
                `<link rel="stylesheet" href="${posInvoiceCssUrl}"><html><body onload="this.print()">`
            );
            newWindow.document.write(invoiceContent);
            newWindow.document.write("</body></html>");
            newWindow.document.close();
        };

        const handleKeyPress = (e) => {
            let pollCount = 0;

            if (
                pollCount < 5 &&
                e.key === "F8" &&
                document.getElementById("pos-invoice")
            ) {
                e.preventDefault();
                setTimeout(() => {
                    printInvoice();
                    onClose();
                    document.querySelector(
                        ".loading-fast-payment"
                    ).style.display = "none";
                    document
                        .querySelectorAll(".bill-filters input[type=search]")[1]
                        .focus();
                    pollCount = 0;
                }, 1);
            } else if (
                e.key === "F8" &&
                !document.getElementById("pos-invoice")
            ) {
                e.preventDefault();
                setTimeout(() => {
                    handleKeyPress(e);
                }, 500);
                pollCount++;
            } else {
                pollCount = 0;
            }
        };

        onMounted(() => {
            document.addEventListener("keydown", handleKeyPress);
        });

        onUnmounted(() => {
            document.removeEventListener("keydown", handleKeyPress);
        });

        return {
            onClose,
            formatDate,
            selectedWarehouse,
            formatAmountCurrency,
            printInvoice,
        };
    },
});
</script>
