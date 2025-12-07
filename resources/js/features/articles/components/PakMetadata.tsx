import type { PakMetadata as PakMetadataType } from "@/types/models";
import PakGenericMetadata from "./pak/PakGenericMetadata";
import { TitleH4 } from "./TitleH4";

type Props = {
  paksMetadata: Record<string, PakMetadataType[]>;
};

export const PakMetadata = ({ paksMetadata }: Props) => {
  if (!paksMetadata || Object.keys(paksMetadata).length === 0) {
    return null;
  }

  return (
    <>
      {Object.entries(paksMetadata).map(([filename, metadataArray]) => (
        <div className="space-y-6" key={filename}>
          <TitleH4>{filename}</TitleH4>
          {metadataArray.map((metadata, index) => (
            <div
              key={`${filename}-${index}`}
              className="order-c-sub/10 pb-6 last:border-b-0 last:pb-0"
            >
              <PakGenericMetadata metadata={metadata} />
            </div>
          ))}
        </div>
      ))}
    </>
  );
};
