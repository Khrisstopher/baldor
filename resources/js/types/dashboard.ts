export type LastActivity = {
    topic: string;
    exercise: number;
    total: number;
} | null;

export type Progress = {
    solved: number;
    total: number;
    topicsDone: number;
    topicsTotal: number;
};

export type TopicSummary = {
    id: number;
    title: string;
    done: number;
    total: number;
};
