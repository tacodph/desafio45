<script setup lang="ts">
defineProps<{
    kind: 'corpo' | 'mente' | 'espirito' | 'amizade' | 'flexoes';
}>();
</script>

<template>
    <svg
        v-if="kind === 'corpo'"
        viewBox="0 0 200 130"
        aria-hidden="true"
        class="h-full w-full max-h-[170px] overflow-visible"
    >
        <path
            d="M0 70 H58 L70 70 L78 46 L90 100 L102 14 L114 86 L122 70 H200"
            fill="none"
            stroke="#3A2622"
            stroke-width="4"
            stroke-linejoin="round"
            stroke-linecap="round"
        />
        <path
            d="M0 70 H58 L70 70 L78 46 L90 100 L102 14 L114 86 L122 70 H200"
            pathLength="100"
            fill="none"
            stroke="#E8432E"
            stroke-width="4.5"
            stroke-linejoin="round"
            stroke-linecap="round"
            style="stroke-dasharray: 34 140; animation: d60-ecg 2.2s linear infinite"
        />
        <rect
            v-for="i in 7"
            :key="i"
            :x="46 + (i - 1) * 16"
            y="108"
            width="10"
            height="18"
            rx="2"
            fill="#E8432E"
            :opacity="0.35 + (i - 1) * 0.09"
            style="transform-box: fill-box; transform-origin: bottom"
            :style="{ animation: `d60-bar 1.6s ease-in-out ${(i - 1) * 0.12}s infinite` }"
        />
    </svg>

    <svg
        v-else-if="kind === 'mente'"
        viewBox="0 0 200 130"
        aria-hidden="true"
        class="h-full w-full max-h-[170px] overflow-visible"
    >
        <circle
            v-for="i in 3"
            :key="i"
            cx="100"
            cy="65"
            r="60"
            fill="none"
            stroke="#9B4FD0"
            stroke-width="2.5"
            style="transform-box: fill-box; transform-origin: center"
            :style="{ animation: `d60-ripple 3.6s ease-out ${(i - 1) * 1.2}s infinite backwards` }"
        />
        <circle
            cx="100"
            cy="65"
            r="22"
            fill="none"
            stroke="#9B4FD0"
            stroke-width="1.5"
            stroke-dasharray="3 5"
            style="transform-box: fill-box; transform-origin: center; animation: d60-spin 14s linear infinite"
        />
        <circle
            cx="100"
            cy="65"
            r="9"
            fill="#9B4FD0"
            style="transform-box: fill-box; transform-origin: center; animation: d60-breathe 3.6s ease-in-out infinite"
        />
    </svg>

    <svg
        v-else-if="kind === 'espirito'"
        viewBox="0 0 200 130"
        aria-hidden="true"
        class="h-full w-full max-h-[170px] overflow-visible"
    >
        <g style="animation: d60-rise 6s ease-in-out infinite">
            <g style="transform-box: view-box; transform-origin: 100px 70px; animation: d60-spin 20s linear infinite">
                <line
                    v-for="i in 12"
                    :key="i"
                    :x1="100 + Math.cos(((i - 1) * Math.PI) / 6) * 32"
                    :y1="70 + Math.sin(((i - 1) * Math.PI) / 6) * 32"
                    :x2="100 + Math.cos(((i - 1) * Math.PI) / 6) * 50"
                    :y2="70 + Math.sin(((i - 1) * Math.PI) / 6) * 50"
                    stroke="#2E8FE0"
                    stroke-width="3.5"
                    stroke-linecap="round"
                />
            </g>
            <circle cx="100" cy="70" r="22" fill="#2E8FE0" />
        </g>
        <polygon
            points="22,128 80,66 98,84 122,56 182,128"
            fill="#1E1B18"
            stroke="#2E8FE0"
            stroke-width="3"
            stroke-linejoin="round"
        />
        <polyline
            points="110,70 122,56 134,70"
            fill="none"
            stroke="#F5F1EA"
            stroke-width="3"
            stroke-linejoin="round"
            stroke-linecap="round"
        />
    </svg>

    <svg
        v-else-if="kind === 'amizade'"
        viewBox="0 0 200 130"
        aria-hidden="true"
        class="h-full w-full max-h-[170px] overflow-visible"
    >
        <circle
            v-for="(deg, i) in [-90, 30, 150]"
            :key="i"
            :cx="100 + Math.cos((deg * Math.PI) / 180) * 18"
            :cy="66 + Math.sin((deg * Math.PI) / 180) * 18"
            r="30"
            fill="rgba(246,168,33,0.10)"
            stroke="#F6A821"
            stroke-width="3"
            :style="{
                '--dx': `${(Math.cos((deg * Math.PI) / 180) * 16).toFixed(1)}px`,
                '--dy': `${(Math.sin((deg * Math.PI) / 180) * 16).toFixed(1)}px`,
                animation: 'd60-unite 3.4s ease-in-out infinite',
            }"
        />
        <circle
            cx="100"
            cy="66"
            r="7"
            fill="#F5F1EA"
            style="transform-box: fill-box; transform-origin: center; animation: d60-spark 3.4s ease-in-out infinite"
        />
    </svg>

    <svg v-else viewBox="0 0 320 176" aria-hidden="true" class="block w-full overflow-visible">
        <line x1="20" y1="120" x2="300" y2="120" stroke="#2E2A25" stroke-width="3" stroke-linecap="round" />
        <line x1="272" y1="116" x2="110" y2="66" stroke="#23B8C4" stroke-width="9" stroke-linecap="round">
            <animate
                attributeName="y2"
                values="66;100;66"
                dur="1.6s"
                repeatCount="indefinite"
                calcMode="spline"
                keyTimes="0;0.5;1"
                keySplines=".45 0 .55 1;.45 0 .55 1"
            />
        </line>
        <polyline
            points="110,66 110,92 110,118"
            fill="none"
            stroke="#23B8C4"
            stroke-width="7"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <animate
                attributeName="points"
                values="110,66 110,92 110,118;110,100 134,108 110,118;110,66 110,92 110,118"
                dur="1.6s"
                repeatCount="indefinite"
                calcMode="spline"
                keyTimes="0;0.5;1"
                keySplines=".45 0 .55 1;.45 0 .55 1"
            />
        </polyline>
        <circle cx="91" cy="59" r="11" fill="#23B8C4">
            <animate
                attributeName="cx"
                values="91;89;91"
                dur="1.6s"
                repeatCount="indefinite"
                calcMode="spline"
                keyTimes="0;0.5;1"
                keySplines=".45 0 .55 1;.45 0 .55 1"
            />
            <animate
                attributeName="cy"
                values="59;98;59"
                dur="1.6s"
                repeatCount="indefinite"
                calcMode="spline"
                keyTimes="0;0.5;1"
                keySplines=".45 0 .55 1;.45 0 .55 1"
            />
        </circle>
        <line x1="98" y1="120" x2="124" y2="120" stroke="#F5F1EA" stroke-width="3" stroke-linecap="round" />
        <rect
            v-for="(n, i) in [30, 45, 60, 75, 90, 100]"
            :key="n"
            :x="26 + i * 47"
            :y="172 - n * 0.4"
            width="38"
            :height="n * 0.4"
            rx="4"
            fill="#2E2A25"
        >
            <animate
                attributeName="fill"
                :values="`#2E2A25;#23B8C4`"
                :keyTimes="`0;${((i + 1) / 7).toFixed(3)}`"
                calcMode="discrete"
                dur="11.2s"
                repeatCount="indefinite"
            />
        </rect>
    </svg>
</template>
