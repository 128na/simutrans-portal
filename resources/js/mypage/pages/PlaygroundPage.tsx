import { createRoot } from "react-dom/client";
import Button from "@/components/ui/Button";
import V2Button from "@/components/ui/V2Button";

const app = document.getElementById("app-playground");

type Condition = Record<string, unknown[]>;

type Pattern<T extends React.ElementType> = {
  name: string;
  props: React.ComponentProps<T>;
};

function generateAllPatterns<T extends React.ElementType>(
  conditions: Condition
): Pattern<T>[] {
  const keys = Object.keys(conditions);
  const patterns: Pattern<T>[] = [];

  function helper(
    index: number,
    currentProps: Record<string, unknown>,
    nameParts: string[]
  ) {
    if (index === keys.length) {
      patterns.push({
        name: nameParts.join(", "),
        props: currentProps as React.ComponentProps<T>,
      });
      return;
    }

    const key = keys[index];
    const values = conditions[key];

    for (const value of values) {
      const newProps = { ...currentProps };
      const newNameParts = [...nameParts];

      if (value !== null) {
        newProps[key] = value;
        newNameParts.push(`${key}=${String(value)}`);
      } else {
        newNameParts.push(`${key}=null`);
      }

      helper(index + 1, newProps, newNameParts);
    }
  }

  helper(0, {}, []);
  return patterns;
}

if (app) {
  const App = () => {
    const conditions: Condition = {
      variant: [
        "primary",
        "primaryOutline",
        "secondary",
        "secondaryOutline",
        "danger",
        "dangerOutline",
        "warn",
        "warnOutline",
        "info",
        "infoOutline",
        "success",
        "successOutline",
      ],
      disabled: [false, true],
    };

    const pattern = generateAllPatterns(conditions) as {
      name: string;
      props: React.ComponentProps<typeof Button>;
    }[];

    return (
      <div className="flex flex-col gap-4">
        {pattern.map((pattern, index) => {
          return (
            <div className="" key={index}>
              <p>{pattern.name}</p>
              <V2Button {...pattern.props}>ボタン</V2Button>
            </div>
          );
        })}
      </div>
    );
  };

  createRoot(app).render(<App />);
}
