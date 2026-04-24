import { defineStore } from "pinia";

export const useCrudStore = defineStore("peer@crud", {
    state: () => ({
        updating: "",
        deleting: "",
        adding: "",
    }),
    actions: {
        addProcess(process: "updating" | "deleting" | "adding", id: string) {
            this[process] = id
        },
        removeProcess(process: "updating" | "deleting" | "adding") {
            this[process] = ""
        }
    }
});
