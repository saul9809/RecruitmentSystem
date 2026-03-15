import DOMPurify from "dompurify"; // npm i dompurify
import { parse } from "marked";
import { useEffect, useRef, useState } from "react";

type ChatMessage = {
    id: number;
    role: "user" | "assistant";
    text: string;
    time: string;
};

function ChatHeader({ toggleId }: { toggleId: string }) {
    return (
        <header className="flex items-center justify-between border-b border-slate-200 bg-white/80 px-5 py-4 backdrop-blur">
            <div>
                <p className="text-xs uppercase tracking-[0.2em] text-slate-500">
                    Cervecería Cubana.CCSA
                </p>
                <h2 className="text-lg font-semibold text-slate-900">
                    Asistente de Reclutamiento
                </h2>
            </div>
            <label
                htmlFor={toggleId}
                className="cursor-pointer rounded-full border border-slate-200 px-3 py-1 text-xs font-medium text-slate-600 transition hover:border-slate-300 hover:text-slate-900"
            >
                Cerrar
            </label>
        </header>
    );
}

function ChatBubble({ message }: { message: ChatMessage }) {
    const isUser = message.role === "user";
    const html = DOMPurify.sanitize(parse(message.text) as string);

    return (
        <div className={isUser ? "ml-auto h-full max-w-[75%]" : "mr-auto w-full max-w-[75%]"}>
            <div
                className={
                    isUser
                        ? "rounded-2xl rounded-tr-sm bg-slate-900 px-4 py-3 text-sm text-white shadow"
                        : "rounded-2xl rounded-tl-sm bg-white px-4 py-3 text-sm text-slate-700 shadow"
                }
            >
                <div dangerouslySetInnerHTML={{ __html: html }} />
            </div>
            <p className={isUser ? "mt-1 text-right text-xs text-slate-400" : "mt-1 text-xs text-slate-400"}>
                {message.time}
            </p>
        </div>
    );
}

export default function ChatWindow() {
    const toggleId = "chat-toggle";
    const [message, setMessage] = useState("");
    const [responses, setResponses] = useState<ChatMessage[]>([]); // <-- inicializado
    const [loading, setLoading] = useState(false);
    const endRef = useRef<HTMLDivElement>(null);

    // auto‑scroll al último mensaje
    useEffect(() => {
        endRef.current?.scrollIntoView({ behavior: "smooth" });
    }, [responses]);

    const now = () =>
        new Date().toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });

    async function handleSend() {
        const text = message.trim();
        if (!text || loading) return;

        // Añade mensaje del usuario
        const userMsg: ChatMessage = {
            id: responses.length + 1,
            role: "user",
            text,
            time: now(),
        };
        setResponses((prev) => [...prev, userMsg]);

        // limpia input y bloquea botón
        setMessage("");
        setLoading(true);

        try {
            const res = await fetch("/invoke-agent", {
                method: "POST",
                body: JSON.stringify({ message: text }),
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN":
                        document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute("content") || "",
                },
            });

            const data = await res.text();
            const assistantMsg: ChatMessage = {
                id: userMsg.id + 1,
                role: "assistant",
                text: data || "_(Sin contenido)_", // fallback si vino vacío
                time: now(),
            };
            setResponses((prev) => [...prev, assistantMsg]);
            // eslint-disable-next-line @typescript-eslint/no-unused-vars
        } catch (err) {
            const assistantMsg: ChatMessage = {
                id: userMsg.id + 1,
                role: "assistant",
                text:
                    "⚠️ Ocurrió un error al procesar tu solicitud. Intenta de nuevo o contacta a soporte.",
                time: now(),
            };
            setResponses((prev) => [...prev, assistantMsg]);
        } finally {
            setLoading(false);
        }
    }

    // Enter = enviar | Shift+Enter = salto de línea (si luego cambias a <textarea>)
    function handleKeyDown(e: React.KeyboardEvent<HTMLInputElement>) {
        if (e.key === "Enter" && !e.shiftKey) {
            e.preventDefault();
            handleSend();
        }
    }

    return (
        <div className="relative">
            <input id={toggleId} type="checkbox" className="peer sr-only" />
            <label
                htmlFor={toggleId}
                className="fixed bottom-6 right-6 z-40 inline-flex h-12 items-center gap-2 rounded-full bg-slate-900 px-4 text-sm font-semibold text-white shadow-lg transition hover:bg-slate-800"
            >
                Asistente de Reclutamiento
                <span className="inline-flex h-2 w-2 rounded-full bg-emerald-400" />
            </label>

            <section className="fixed bottom-24 right-6 z-40 hidden w-90 max-w-[92vw] flex-col overflow-hidden rounded-2xl border border-slate-200 bg-linear-to-br from-slate-50 via-white to-amber-50 shadow-xl peer-checked:flex">
                <ChatHeader toggleId={toggleId} />

                <div className="max-h-90 space-y-4 overflow-y-auto px-5 py-6">
                    {responses.map((m) => (
                        <ChatBubble key={m.id} message={m} />
                    ))}
                    <div ref={endRef} />
                </div>

                <div className="border-t border-slate-200 bg-white px-5 py-4">
                    <div className="flex items-center gap-3">
                        <input
                            type="text"
                            value={message}
                            onInput={(e) => setMessage((e.target as HTMLInputElement).value)}
                            onKeyDown={handleKeyDown}
                            placeholder={loading ? "Generando respuesta…" : "Escribe tu mensaje"}
                            className="flex-1 rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 outline-none disabled:opacity-60"
                            disabled={loading}
                        />
                        <button
                            onClick={handleSend}
                            type="button"
                            disabled={loading}
                            className="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white disabled:opacity-60"
                        >
                            {loading ? "Enviando…" : "Enviar"}
                        </button>
                    </div>
                </div>
            </section>
        </div>
    );
}
