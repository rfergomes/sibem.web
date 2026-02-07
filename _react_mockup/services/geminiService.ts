
import { GoogleGenAI } from "@google/genai";

const ai = new GoogleGenAI({ apiKey: process.env.API_KEY });

export const getDailyVerse = async () => {
  try {
    const response = await ai.models.generateContent({
      model: 'gemini-3-flash-preview',
      contents: 'Sugira um versículo bíblico aleatório para encorajamento em um sistema administrativo de igreja, em português.',
      config: {
          systemInstruction: 'Você é um assistente espiritual para administradores de igreja.',
          temperature: 0.8
      }
    });
    return response.text;
  } catch (error) {
    console.error("Erro ao obter versículo da IA:", error);
    return "Lâmpada para os meus pés é tua palavra, e luz para o meu caminho. (Salmos 119:105)";
  }
};

export const summarizeInventoryReport = async (stats: any) => {
    // Potential use for summarizing findings
    return "Relatório gerado com sucesso.";
}
