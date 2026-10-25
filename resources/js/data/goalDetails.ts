import { images } from '@/data/images';

export const GOAL_KEYS = ['alimentacao', 'treino', 'hidratacao', 'habilidade', 'plus'] as const;

export type GoalKey = (typeof GOAL_KEYS)[number];

export type GoalDetail = {
    key: GoalKey;
    color: string;
    metric: string;
    nav: string;
    title: string;
    desc: string;
    icon: string;
    pillar: string;
    rule: string[];
    why: string[];
    tips: string[];
};

export const GOAL_DETAILS: GoalDetail[] = [
    {
        key: 'alimentacao',
        color: '#43A047',
        metric: 'Diário',
        nav: 'Alimentação',
        title: 'Alimentação e organização',
        desc: 'Diário. Sem álcool, fast food e doce, com 1 refeição luxo e 1 Plus por semana.',
        icon: images.alimentacao,
        pillar: 'Diário',
        rule: [
            'Aqui a regra é transformar a alimentação em um sistema de organização semanal simples, com autonomia para escolher o que você gosta.',
            'A ideia não é exigir que você cozinhe pratos elaborados nem que tenha uma variedade enorme de alimentos todos os dias. É ensinar a planejar, facilitar as escolhas e reduzir a dependência de ultraprocessados, hipercalóricos e opções que atrapalham a sua evolução.',
            'A regra: planejar para facilitar',
            'A cada semana, você vai escolher os alimentos que pretende consumir e definir como vai prepará-los. Não precisa inventar receitas novas todos os dias nem passar horas na cozinha.',
            'O objetivo é ter comida de verdade disponível, facilitar as refeições e evitar depender de qualquer coisa que aparecer pela frente.',
        ],
        why: [
            'Alimentação organizada dá energia real. A diferença aparece no treino e no sono.',
            'A refeição luxo é o momento sem culpa. A Plus entra no dia sem virar uma segunda refeição.',
        ],
        tips: [
            'No domingo, defina o cardápio e onde vai comer em cada refeição.',
            'Monte a lista de compras a partir do cardápio e do que já tem em casa.',
            'Separe um tempo curto para proteínas, legumes e lanches.',
            'Tenha um plano para a correria: lanche na bolsa ou um pedido equilibrado já escolhido.',
            'Escolha a refeição luxo e a Plus no começo da semana.',
            'Doce industrializado: chocolate, bombom, refrigerante, sorvete de pote, bolo de padaria. Na refeição luxo, tudo vale.',
            'Fast food: lanches, frituras e comida de rua. Uma refeição em restaurante normal vale.',
        ],
    },
    {
        key: 'treino',
        color: '#E53935',
        metric: '45 min',
        nav: 'Treino',
        title: 'Movimento e treino',
        desc: 'Diário. A referência é 45 minutos, ajustada ao seu nível.',
        icon: images.treino,
        pillar: 'Diário',
        rule: [
            'Manter o movimento todos os dias. A referência é 45 minutos de treino ou movimento.',
            'A meta se ajusta ao nível declarado no dia 1: iniciante 20 a 30 min, intermediário 30 a 45 min, avançado 45 min ou mais.',
            'Dá para subir de nível no dia 15 ou no dia 30.',
            'No check-in até 23h, manda a foto do treino ou do rosto suado.',
        ],
        why: [
            'O treino cria rotina, dá confiança e mostra que você consegue.',
            'O desafio premia consistência, não um treino perfeito isolado.',
            'Qualquer modalidade vale: musculação, corrida, futebol, natação, yoga, dança.',
        ],
        tips: [
            'Treinar ao ar livre é recomendado: parque, praia ou rua.',
            'Dá para dividir. 30 minutos de manhã e 15 à noite contam.',
            'Dia ruim ainda conta: fez menos, avise o grupo e siga.',
            'Deixe a roupa pronta na noite anterior.',
        ],
    },
    {
        key: 'hidratacao',
        color: '#1E88E5',
        metric: 'Água + sono',
        nav: 'Hidratação',
        title: 'Hidratação e sono',
        desc: 'Diário. Meta de água e pelo menos 7 horas de sono.',
        icon: images.hidratacao,
        pillar: 'Diário',
        rule: [
            'Hidratação: meta individual de cerca de 35 ml por kg, ou a meta fixa de 2,5 a 3 litros.',
            'Sono: pelo menos 7 horas, ou o horário combinado com tolerância de 30 minutos.',
            'O dia fecha quando as duas metas são batidas. Na pontuação, cada metade vale 1 ponto.',
        ],
        why: [
            'Água e sono sustentam o treino e o humor, separados das regras da alimentação.',
            'O cantinho da nutri traz listas, cardápios e opções práticas.',
        ],
        tips: [
            'Garrafa à vista, um copo a cada refeição e água ao acordar.',
            'Um copo de 250 ml: 3 litros são 12 copos.',
            'Telas desligadas antes de deitar, quarto escuro e cafeína só até o meio da tarde.',
        ],
    },
    {
        key: 'habilidade',
        color: '#8E24AA',
        metric: '3x / sem',
        nav: 'Habilidade',
        title: 'Habilidade nova',
        desc: 'Autonomia na rotina. Uma habilidade, pelo menos 3 vezes por semana.',
        icon: images.habilidade,
        pillar: '3x por semana',
        rule: [
            'No dia 1, escolha uma habilidade concreta. Exemplo: aprender 5 pratos saudáveis ou preparar as marmitas da semana.',
            'Pratique pelo menos 3 vezes por semana.',
            'Mostre a evolução no grupo no dia 45.',
        ],
        why: [
            'O objetivo é resolver situações reais: cozinhar, pedir algo equilibrado, organizar a semana.',
            'Três ideias por semana dão foco: você pratica o que escolheu, sem começar tudo ao mesmo tempo e não terminar nada.',
        ],
        tips: [
            'Defina bem: não “aprender a cozinhar”, mas “um prato novo por semana”.',
            'Falar a escolha para o grupo gera compromisso.',
            'Registre foto ou vídeo na primeira semana e nos dias 15, 30 e 45.',
        ],
    },
    {
        key: 'plus',
        color: '#F5B800',
        metric: '1x / sem',
        nav: 'Plus',
        title: 'Objetivo Plus',
        desc: 'Uma por semana. Um alimento cultural, com pontos extras.',
        icon: images.plus,
        pillar: 'A qualquer momento',
        rule: [
            'Um alimento cultural por semana, não uma refeição inteira.',
            'A meta é pelo menos 1 por semana. Até 2 por semana rendem +5 pontos cada.',
            'Não é obrigatório para fechar o dia, mas conta no ranking.',
        ],
        why: [
            'O desafio continua divertido sem transformar comida e exercício em punição.',
            'Sentar à mesa, experimentar algo novo ou preparar um café com calma também é consistência.',
        ],
        tips: [
            'Uma fatia de bolo de manhã ou à tarde.',
            'Uma porção de pão de queijo.',
            'Pipoca com a família.',
        ],
    },
];

export function goalDetail(key: string): GoalDetail | undefined {
    return GOAL_DETAILS.find((goal) => goal.key === key);
}
