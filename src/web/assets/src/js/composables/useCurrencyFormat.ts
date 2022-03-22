export const format = (amount: String) => {
    if (!amount) {
        return amount
    }

    return amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, "$&,");
}