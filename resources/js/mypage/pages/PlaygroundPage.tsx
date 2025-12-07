import { createRoot } from "react-dom/client";
import V2Button from "@/components/ui/v2/V2Button";
import V2Card from "@/components/ui/v2/V2Card";
import { ButtonHTMLAttributes } from "react";
import { JSX } from "react/jsx-runtime";

const app = document.getElementById("app-playground");

type Condition = Record<string, unknown[] | undefined>;
type Pattern<T extends React.ElementType> = {
  name: string;
  props: React.ComponentProps<T>;
};

type P = JSX.IntrinsicAttributes &
  ButtonHTMLAttributes<HTMLButtonElement> & {
    variant?: string;
    children: React.ReactNode;
  };

function generateAllPatterns<T extends React.ElementType>(
  conditions: Condition
): Pattern<T>[] {
  const keys = Object.keys(conditions).filter(
    (key) => conditions[key] !== undefined
  );
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

    if (!values) return;

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
    const components = [
      {
        name: "V2Button",
        render: (p: P) => <V2Button {...p}>ボタン</V2Button>,
        condition: {
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
        },
      },
      {
        name: "V2Button",
        render: (p: P) => (
          <V2Card {...p}>
            <h3>カード</h3>
            <div>これはV2Cardコンポーネントの内容です。</div>
          </V2Card>
        ),
        condition: {
          variant: [
            "primary",
            "secondary",
            "danger",
            "warn",
            "info",
            "success",
          ],
        },
      },
    ];

    return (
      <div className="flex flex-col gap-4">
        {components.map((component, componentIndex) => {
          const pattern = generateAllPatterns(component.condition);
          return (
            <div key={componentIndex}>
              <p className="font-bold">{component.name}</p>
              {pattern.map((pattern, patternIndex) => {
                return (
                  <div key={patternIndex} className="mb-4">
                    <p>{pattern.name}</p>
                    {component.render(pattern.props)}
                  </div>
                );
              })}
            </div>
          );
        })}
      </div>
    );
  };

  createRoot(app).render(<App />);
}
