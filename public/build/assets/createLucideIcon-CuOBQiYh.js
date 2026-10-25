import{N as s}from"./app-JUtb-2uq.js";const u={logo:"/images/logo.png",helen:"/images/helen-freitas.jpg",treino:"/images/treino_movimento.png",hidratacao:"/images/hidratacao_sono.png",habilidade:"/images/habilidade_nova.png",corpo:"/images/corpo_forte.png",plus:"/images/objetivo_plus.png"};/**
 * @license lucide-vue-next v0.468.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const d=e=>e.replace(/([a-z0-9])([A-Z])/g,"$1-$2").toLowerCase();/**
 * @license lucide-vue-next v0.468.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */var a={xmlns:"http://www.w3.org/2000/svg",width:24,height:24,viewBox:"0 0 24 24",fill:"none",stroke:"currentColor","stroke-width":2,"stroke-linecap":"round","stroke-linejoin":"round"};/**
 * @license lucide-vue-next v0.468.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const h=({size:e,strokeWidth:o=2,absoluteStrokeWidth:t,color:i,iconNode:n,name:g,class:m,...c},{slots:r})=>s("svg",{...a,width:e||a.width,height:e||a.height,stroke:i||a.stroke,"stroke-width":t?Number(o)*24/Number(e):o,class:["lucide",`lucide-${d(g??"icon")}`],...c},[...n.map(l=>s(...l)),...r.default?[r.default()]:[]]);/**
 * @license lucide-vue-next v0.468.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const w=(e,o)=>(t,{slots:i})=>s(h,{...t,iconNode:o,name:e},i);export{w as c,u as i};
