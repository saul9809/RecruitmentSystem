import { router } from "@inertiajs/react";
import { Upload, FileText, CheckCircle, Loader2 } from "lucide-react";
import { useState, useRef } from "react";
import type React from "react";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import { Progress } from "@/components/ui/progress";
import { Separator } from "@/components/ui/separator";

interface UploadItem {
  id: string;
  name: string;
  progress: number;
  status: "uploading" | "completed";
}

export default function FileUpload06() {
  const [uploads, setUploads] = useState<UploadItem[]>([]);
  const [files, setFiles] = useState<File[]>([]);
  const [processing, setProcessing] = useState(false);

  const filePickerRef = useRef<HTMLInputElement>(null);

  const openFilePicker = () => {
    filePickerRef.current?.click();
  };

  // ✅ UI SE ACTIVA INMEDIATAMENTE AL SELECCIONAR ARCHIVOS
  const onFileInputChange = (event: React.ChangeEvent<HTMLInputElement>) => {
    if (!event.target.files) return;

    const selected = Array.from(event.target.files);
    setFiles(selected);

    setUploads(
      selected.map((file, index) => ({
        id: `${index}-${file.name}`,
        name: file.name,
        progress: 100, // ✅ COMPLETADO VISUAL INMEDIATO
        status: "completed",
      }))
    );
  };

  const onDragOver = (event: React.DragEvent) => {
    event.preventDefault();
  };

  // ✅ MISMO COMPORTAMIENTO AL ARRASTRAR
  const onDropFiles = (event: React.DragEvent) => {
    event.preventDefault();

    const dropped = Array.from(event.dataTransfer.files).filter(
      (file) => file.type === "application/pdf"
    );

    if (dropped.length === 0) return;

    setFiles(dropped);

    setUploads(
      dropped.map((file, index) => ({
        id: `${index}-${file.name}`,
        name: file.name,
        progress: 100,
        status: "completed",
      }))
    );
  };

  // ✅ BACKEND SOLO PROCESA, NO CONTROLA LA UI
  const startUpload = () => {
    if (files.length === 0) return;

    const formData = new FormData();
    files.forEach((file) => {
      formData.append("docs[]", file);
    });

    setProcessing(true);

    router.post("/cvs/upload", formData, {
      forceFormData: true,

      onSuccess: (page) => {
        console.log("✅ Backend procesó:", page.props);
      },

      onError: (errors) => {
        console.error("❌ Error backend:", errors);
      },

      onFinish: () => {
        setProcessing(false);
      },
    });
  };

  const activeUploads = uploads.filter((f) => f.status === "uploading");
  const completedUploads = uploads.filter((f) => f.status === "completed");

  return (
    <div className="flex flex-col gap-y-4">
      {/* DROPZONE */}
      <Card
        className="group flex w-full flex-col items-center justify-center gap-4 py-8 border-dashed cursor-pointer bg-white hover:bg-muted/50 transition-colors"
        onDragOver={onDragOver}
        onDrop={onDropFiles}
        onClick={openFilePicker}
      >
        <Upload className="size-5 text-muted-foreground" />

        <div className="text-muted-foreground text-sm text-center">
          Arrastre los archivos acá o{" "}
          <Button
            variant="link"
            className="p-0 h-auto"
            onClick={openFilePicker}
          >
            búsquelos
          </Button>
        </div>

        <input
          ref={filePickerRef}
          type="file"
          className="hidden"
          accept="application/pdf"
          multiple
          onChange={onFileInputChange}
        />

        <span className="text-xs text-muted-foreground">
          Soportado: PDF (máx. 10MB)
        </span>
      </Card>

      {/* BOTÓN DE PROCESAR */}
      <Button
        className="mt-2"
        disabled={processing || files.length === 0}
        onClick={startUpload}
      >
        {processing ? "Procesando..." : "Procesar CVs"}
      </Button>

      {/* LISTADO */}
      <div className="flex flex-col gap-y-4 mt-4">
        {activeUploads.length > 0 && (
          <div>
            <h2 className="flex items-center text-sm mb-2">
              <Loader2 className="size-4 mr-1 animate-spin" />
              Subiendo
            </h2>

            {activeUploads.map((file) => (
              <div key={file.id} className="flex items-center gap-2 mb-2">
                <FileText className="size-4" />
                <span className="flex-1 text-sm">{file.name}</span>
                <Progress value={file.progress} className="w-24 h-2" />
              </div>
            ))}
          </div>
        )}

        {activeUploads.length > 0 && completedUploads.length > 0 && (
          <Separator />
        )}

        {completedUploads.length > 0 && (
          <div>
            <h2 className="flex items-center text-sm mb-2 text-green-600">
              <CheckCircle className="size-4 mr-1" />
              Completado
            </h2>

            {completedUploads.map((file) => (
              <div key={file.id} className="flex items-center gap-2 mb-2">
                <FileText className="size-4" />
                <span className="flex-1 text-sm">{file.name}</span>
                <Progress value={file.progress} className="w-24 h-2" />
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}