import {resolve} from "path";
import {defineConfig} from "vite";
import vue from "@vitejs/plugin-vue";
import i18nExtractKeys from "./lib/i18nExtractKeys.vite.js";

// https://vitejs.dev/config/
export default defineConfig({
    target: "es2016",
    plugins: [i18nExtractKeys(), vue()],
    build: {
        lib: {
            entry: resolve(__dirname, "resources/js/igsn-main.js"),
            name: "pidManagerIgsn",
            fileName: "build-igsn",
            formats: ["iife"],
        },
        outDir: resolve(__dirname, "public/build"),
        rollupOptions: {
            external: ["vue"],
            output: {
                globals: {
                    vue: "pkp.modules.vue",
                },
            },
        },
    },
    resolve: {
        alias: {
            '@': resolve(__dirname, "../../../lib/ui-library/src"),
        }
    }
});
