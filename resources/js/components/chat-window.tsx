import { parse } from "marked";
import { useState } from "react";

type ChatMessage = {
    id?: number;
    role: 'user' | 'assistant';
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
    const isUser = message.role === 'user';
    const messageHtml = parse(message?.text);
    return (
        <div
            className={
                isUser
                    ? 'ml-auto h-full max-w-[75%]'
                    : 'mr-auto w-full max-w-[75%]'
            }
        >
            <div
                className={
                    isUser
                        ? 'rounded-2xl rounded-tr-sm bg-slate-900 px-4 py-3 text-sm text-white shadow'
                        : 'rounded-2xl rounded-tl-sm bg-white px-4 py-3 text-sm text-slate-700 shadow'
                }
            >
                <div dangerouslySetInnerHTML={{ __html: messageHtml }} />
            </div>
            <p
                className={
                    isUser
                        ? 'mt-1 text-right text-xs text-slate-400'
                        : 'mt-1 text-xs text-slate-400'
                }
            >
                {message.time}
            </p>
        </div>
    );
}

export default function ChatWindow() {
    const toggleId = 'chat-toggle';
    const [message, setMessage] = useState('');
    const [responses, setResponses] = useState<ChatMessage[]>();

    // -- Fetch de api agent
    function handleSend() {
        setResponses((prev) => [
            ...(prev || []),
            {
                id: prev ? prev.length + 1 : 1,
                role: 'user',
                text: message,
                time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
            }
        ])
        fetch('invoke-agent', {
            method: 'POST',
            body: JSON.stringify({ message }),
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        }).then(res => res.text()).then(data => {
            setResponses((prev) => [
                ...(prev || []),
                {
                    id: prev ? prev.length + 1 : 1,
                    role: 'assistant',
                    text: data,
                    time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                }
            ])
        });
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
                    {responses?.map((message) => (
                        <ChatBubble key={message.id} message={message} />
                    ))}
                </div>

                <div className="border-t border-slate-200 bg-white px-5 py-4">
                    <div className="flex items-center gap-3">
                        <input
                            type="text"
                            value={message}
                            onInput={e => setMessage(e.currentTarget.value)}
                            placeholder="Candidatos Top"
                            className="flex-1 rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 outline-none"
                        />
                        <button
                            onClick={handleSend}
                            type="submit"
                            className="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white"
                        >
                            Enviar
                        </button>
                    </div>
                </div>
            </section>
        </div>
    );
}
