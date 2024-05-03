<template>
    <div>
        <a-button type="text" size="small" @click="showModal">
            <template #icon>
                <BarcodeOutlined />
            </template>
            {{ $t("product.view_barcode") }}
        </a-button>

        <div id="printThis" style="display: none">
            <div style="display: flex; flex-wrap: wrap; gap: 20px;">
                <div v-for="n in 24" style="text-align: center; font: 18px monospace; line-height: 1;">
                    <BarcodeGenerator
                        :key="n"
                        :value="itemCode"
                        :format="barcodeSymbology"
                        :height="50"
                        :margin="0"
                        :width="width"
                        :fontSize="fontSize"
                        :elementTag="'svg'"
                    />
                    <div style="margin-top: -2px;">
                        <div v-if="isNameIncluded">{{ itemName }}</div>
                        <div v-if="isQuantityIncluded">{{ itemQuantity }}</div>
                        <div v-if="isPriceIncluded">{{ itemSalesTotalPrice }}{{ currencySymbol }}</div>
                        <div v-if="hasExpirationDate">{{ $t("product.expiry_date") }}: {{ itemExpirationDate }}</div>
                    </div>
                </div>
            </div>
        </div>

        <a-modal
            v-model:open="visible"
            :title="$t('product.barcode')"
            :width="520 + width * 100 * (1 / width)"
            :footer="false"
            @ok="handleOk"
        >
            <p style="text-align: center; direction: ltr; font: 18px monospace; line-height: 1;">
                <BarcodeGenerator
                    :value="itemCode"
                    :format="barcodeSymbology"
                    :height="50"
                    :margin="0"
                    :width="width"
                    :fontSize="fontSize"
                    :elementTag="'svg'"
                />
                <div style="margin-top: -2px;">
                        <div v-if="isNameIncluded">{{ itemName }}</div>
                        <div v-if="isQuantityIncluded">{{ itemQuantity }}</div>
                        <div v-if="isPriceIncluded">{{ itemSalesTotalPrice }}{{ currencySymbol }}</div>
                        <div v-if="hasExpirationDate">{{ $t("product.expiry_date") }}: {{ itemExpirationDate }}</div>
                </div>
            </p>
            <a-button type="primary" @click="printDiv('printThis')" block>
                <template #icon>
                    <PrinterOutlined />
                </template>
                {{ $t("product.print_barcode") }}
            </a-button>
        </a-modal>
    </div>
</template>
<script>
import { defineComponent, ref, onMounted } from "vue";
import { BarcodeOutlined, PrinterOutlined } from "@ant-design/icons-vue";
import BarcodeGenerator from "../../../../common/components/barcode/BarcodeGenerator.vue";
import common from "../../../../common/composable/common";

export default defineComponent({
    props: [
        "itemName",
        "itemQuantity",
        "itemExpirationDate",
        "itemCode",
        "isNameIncluded",
        "isQuantityIncluded",
        "hasExpirationDate",
        "isPriceIncluded",
        "itemSalesTotalPrice",
        "currencySymbol",
        "barcodeSymbology",
    ],
    components: {
        BarcodeOutlined,
        PrinterOutlined,
        BarcodeGenerator,
    },
    setup(props) {
        const width = ref(2);
        const fontSize = ref(20);
        const text = ref("");

        const visible = ref(false);
        const { appSetting } = common();

        const showModal = () => {
            visible.value = true;
        };

        const handleOk = (e) => {
            visible.value = false;
        };

        const printDiv = (divName) => {
            let divContents = document.getElementById(divName).innerHTML;
            let newWindow = window.open("", "", "height=500, width=500");

            newWindow.document.write("<body >");
            newWindow.document.write(divContents);
            newWindow.document.write("</body></html>");
            newWindow.document.close();
            newWindow.print();
        };

        onMounted(() => {
        });

        return {
            visible,
            showModal,
            handleOk,
            printDiv,
            appSetting,
            width,
            fontSize,
            text,
        };
    },
});
</script>
