export const GOAL_KEYS = ['treino', 'alimentacao', 'oracao', 'habilidade', 'flexoes'] as const;

export type GoalKey = (typeof GOAL_KEYS)[number];

export type GoalDetail = {
    key: GoalKey;
    color: string;
    metric: string;
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
        key: 'treino',
        color: '#E8432E',
        metric: '45 min',
        title: 'Treino e movimento',
        desc: 'Qualquer modalidade. Pelo menos 2x por semana ao ar livre.',
        icon: '/images/goals/treino.webp',
        pillar: 'Corpo',
        rule: [
            'Todo dia: pelo menos 45 minutos de treino ou movimento.',
            'Vale academia, corrida, bike, futebol, natação, luta, yoga, caminhada forte — o que fizer você suar e se mexer de verdade.',
            'Pelo menos 2 vezes por semana o treino precisa ser ao ar livre.',
            'No check-in até 23h, manda a foto + o que fez no dia.',
        ],
        why: [
            'O corpo responde a consistência, não a um treino perfeito uma vez por semana.',
            'Sair de casa 2x por semana quebra a rotina fechada, melhora o humor e reforça o compromisso público com o desafio.',
            '45 minutos é o tempo em que dá para treinar de verdade sem virar desculpa nem virar obsessão.',
        ],
        tips: [
            'Se o dia apertar, quebre em 2 blocos (ex.: 25 + 20). O que conta é completar os 45 minutos.',
            'Deixe a roupa pronta na noite anterior. Menos decisão, mais ação.',
            'Marque na agenda os 2 treinos ao ar livre da semana — domingo, se possível.',
            'Caminhada vale se for firme: ritmo que eleve o coração, não um passeio no celular.',
        ],
    },
    {
        key: 'alimentacao',
        color: '#5DB33A',
        metric: '3L de água',
        title: 'Alimentação e hidratação',
        desc: 'Sem álcool, fast food e doce industrializado. 1 refeição luxo por semana.',
        icon: '/images/goals/alimentacao.webp',
        pillar: 'Corpo',
        rule: [
            'Beba 3 litros de água por dia.',
            'Fora da refeição luxo: sem fast food, sem doce industrializado e sem álcool.',
            '1 refeição luxo por semana — só na sexta à noite, sábado ou domingo. Nessa refeição pode ser o que quiser.',
            'Álcool só nos 3 eventos que você declarar no dia 1 do desafio. Fora isso, não entra.',
        ],
        why: [
            'Hidratação muda energia, pele, treino e fome. 3 litros é a âncora diária mais simples do desafio.',
            'Tirar fast food e doce industrializado corta o ciclo de pico e queda. A refeição luxo existe para o plano ser sustentável.',
            'Limitar o álcool a 3 eventos declarados tira a negociação diária e coloca o prazer no lugar certo: celebração, não escape.',
        ],
        tips: [
            'Encha 3 garrafas de 1L de manhã. Se as três acabaram, a meta de água está feita.',
            'Doce caseiro de fruta não é o alvo. O que sai é o industrializado: refrigerante, chocolate de supermercado, bolacha, sorvete de paçoca de mercado.',
            'Escolha o dia da refeição luxo no começo da semana. Assim o restante dos dias não vira “quase luxo”.',
            'No dia 1, anote os 3 eventos com álcool. Se não anotou, não vale depois.',
        ],
    },
    {
        key: 'oracao',
        color: '#2E8FE0',
        metric: '10 + 30 min',
        title: 'Fé e família',
        desc: 'Oração diária, missa ou culto semanal e família sem celular.',
        icon: '/images/goals/oracao.webp',
        pillar: 'Fé',
        rule: [
            '10 minutos de oração todo dia — de manhã, à noite ou no intervalo que você conseguir proteger.',
            'Missa ou culto 1 vez por semana.',
            '30 minutos por dia de presença com a família, sem celular. Vale conversa, jantar, brincadeira, caminhada junto.',
            'Quem mora sozinho: 30 minutos de ligação, vídeo ou encontro com alguém da família (ou o vínculo mais próximo que você tem).',
        ],
        why: [
            'O desafio não é só corpo. Fé e vínculo são o que segura a disciplina quando a motivação cai.',
            'Dez minutos diários criam o hábito. A missa ou o culto semanal tira a fé do automático.',
            'Família sem celular é treino de presença. É o oposto do scroll — e é o que a gente quer lembrar no verão.',
        ],
        tips: [
            'Deixe o celular em outro cômodo nos 30 minutos. Se estiver na mesa, a regra quebrou.',
            'Use um alarme fixo para os 10 minutos de oração. Hábito pega no horário, não na vontade.',
            'Se perder a missa/culto do domingo, reponha na semana. Não acumule para “o mês que vem”.',
            'Avise em casa: “são 30 minutos nossos, sem tela”. O combinado evita briga e vira ritual.',
        ],
    },
    {
        key: 'habilidade',
        color: '#9B4FD0',
        metric: '15 min',
        title: 'Habilidade nova',
        desc: 'Escolha no dia 1, pratique todo dia, mostre no dia 45.',
        icon: '/images/goals/habilidade.webp',
        pillar: 'Conhecimento',
        rule: [
            'No dia 1, escolha UMA habilidade nova. Não troca no meio do caminho.',
            'Pratique pelo menos 15 minutos todos os dias.',
            'No dia 45, mostre o resultado: vídeo, foto, áudio, texto — alguma prova de que você avançou.',
            'Tem que ser nova de verdade: algo que você ainda não pratica no piloto automático.',
        ],
        why: [
            'Quinze minutos por dia, 45 dias, são cerca de 11 horas de foco. Dá para sair do zero e ter o que mostrar.',
            'Uma só habilidade evita a armadilha de começar cinco coisas e não terminar nenhuma.',
            'Mostrar no dia 45 cria accountability. Não é para impressionar: é para não mentir para você mesmo.',
        ],
        tips: [
            'Exemplos que funcionam: violão, inglês, programação, desenho, xadrez, fotografia, um livro difícil, culinária de verdade.',
            'Prepare o material na noite anterior (caderno, app, instrumento). Se tiver que “achar”, você pula o dia.',
            'Grave um take rápido toda semana. No dia 45 você compara o dia 1 com o agora — e se surpreende.',
            'Se travar, baixe a régua: 15 minutos ruins ainda contam. 0 minutos não conta.',
        ],
    },
    {
        key: 'flexoes',
        color: '#23B8C4',
        metric: '30 → 100',
        title: 'Flexões progressivas',
        desc: 'A meta sobe a cada 8 dias. 2.900 no total.',
        icon: '/images/goals/flexoes.webp',
        pillar: 'Corpo',
        rule: [
            'A meta diária sobe a cada 8 dias: 30, 45, 60, 75, 90 e 100 flexões (os 5 últimos dias são de 100).',
            'Pode dividir em séries ao longo do dia. O que conta é o total do dia.',
            'Do dia 1 ao 45, o acumulado é de 2.900 flexões.',
            'No check-in, registra que cumpriu a meta do dia.',
        ],
        why: [
            'Começar com 30 é acessível. Terminar com 100 prova que o corpo mudou — e a cabeça também.',
            'A progressão de 8 em 8 dias evita lesão de ego (querer 100 no dia 1) e evita acomodação.',
            'É o objetivo mais visível do grupo: todo mundo entende o número. Por isso funciona tão bem no Gymrats.',
        ],
        tips: [
            'Joelho no chão no começo vale se a forma estiver certa. Melhor 30 bem feitas do que 10 caindo.',
            'Espalhe: 3 séries de manhã, 2 à tarde, o resto à noite. O dia inteiro é o ringue.',
            'Em grupo, criem o grupo no Gymrats para acompanhar a evolução juntos.',
            'Use o slider nesta página para ver a meta exata de cada dia antes de começar.',
        ],
    },
];

export function findGoal(key: string): GoalDetail | undefined {
    return GOAL_DETAILS.find((goal) => goal.key === key);
}
