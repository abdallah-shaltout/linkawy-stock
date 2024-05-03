import { reactive } from "vue";
import { useI18n } from "vue-i18n";

const fields = () => {
    const addEditUrl = "stock-adjustments";
    const url =
        "stock-adjustments?fields=xid,product_id,x_product_id,product{id,xid,name,image,image_url},quantity,adjustment_type,notes,created_at,updated_at";
    const hashableColumns = ["product_id"];
    const { t } = useI18n();

    const initData = {
        product_id: undefined,
        quantity: 1,
        notes: "",
        adjustment_type: "add",
    };

    const columns = [
        {
            title: t("product.product"),
            dataIndex: "product_id",
        },
        {
            title: t("stock_adjustment.quantity"),
            dataIndex: "quantity",
        },
        {
            title: t("stock_adjustment.notes"),
            dataIndex: "notes",
        },
        {
            title: t("user.created_at"),
            dataIndex: "created_at",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const adjustmentTypes = [
        {
            key: "add",
            value: t("stock_adjustment.adjustment_add"),
        },
        {
            key: "subtract",
            value: t("stock_adjustment.adjustment_subtract"),
        },
    ];

    const filters = reactive({
        product_id: undefined,
    });

    return {
        url,
        addEditUrl,
        hashableColumns,
        initData,
        columns,
        adjustmentTypes,
        filters,
    };
};

export default fields;
