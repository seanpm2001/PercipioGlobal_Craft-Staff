export const format = (amount: string) => {
    if (!amount) {
        return amount
    }

    return parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, "$&,");
}