import { Accordion } from "@/components/ui/Accordion";
import type { PakMetadata as PakMetadataType } from "@/types/models";
import PakGenericMetadata from "./pak/PakGenericMetadata";
import React from "react";
import { TitleH4 } from "./TitleH4";

type Props = {
  paksMetadata: Record<string, PakMetadataType[]>;
};

export const PakMetadata = ({ paksMetadata }: Props) => {
  if (!paksMetadata || Object.keys(paksMetadata).length === 0) {
    return null;
  }

  return (
    <div className="mt-4">
      <Accordion title="Pakファイル">
        {Object.entries(paksMetadata).map(([filename, metadataArray]) => (
          <div className="space-y-6">
            <TitleH4>{filename}</TitleH4>
            {metadataArray.map((metadata, index) => (
              <div
                key={`${filename}-${index}`}
                className="border-b border-gray-200 pb-6 last:border-b-0 last:pb-0"
              >
                <div>
                  {metadata.name}
                  <span className="text-sm ml-1 font-normal text-gray-500">
                    ({metadata.objectType})
                  </span>
                </div>
                <PakGenericMetadata metadata={metadata} />
              </div>
            ))}
          </div>
        ))}
      </Accordion>
    </div>
  );
};
