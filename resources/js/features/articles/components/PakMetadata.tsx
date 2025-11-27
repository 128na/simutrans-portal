import { Accordion } from "@/components/ui/Accordion";
import type { PakMetadata as PakMetadataType } from "@/types/models";
import { PakVehicleMetadata } from "./pak/PakVehicleMetadata";
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
                {metadata.objectType === "vehicle" && metadata.vehicleData ? (
                  <PakVehicleMetadata
                    metadata={metadata}
                    vehicleData={metadata.vehicleData}
                  />
                ) : (
                  <div className="mt-2 p-4 bg-gray-50 rounded">
                    <p className="text-gray-600">
                      この種類のアドオン詳細情報は準備中です
                    </p>
                    {metadata.copyright && (
                      <p className="mt-2 text-sm text-gray-500">
                        作者: {metadata.copyright}
                      </p>
                    )}
                    <p className="text-sm text-gray-500">
                      タイプ: {metadata.objectType}
                    </p>
                    <p className="text-sm text-gray-500">
                      バージョン: {metadata.compilerVersionCode}
                    </p>
                  </div>
                )}
              </div>
            ))}
          </div>
        ))}
      </Accordion>
    </div>
  );
};
